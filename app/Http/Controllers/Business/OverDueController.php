<?php

namespace App\Http\Controllers\Business;

use App\Models\OverDue;
use App\Models\Business;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Models\Businessjournal;
use App\Models\AccountReceivable;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountReceivablePayment;

class OverDueController extends Controller
{
    public static function updateOrCreate($business, $accountReceivable): void
    {
        //jika tanggal sekarang lebih besar dari jatuh tempo temporari (tempo bulanan) maka buat, perbarui atau ubah data pada table over due
        $now = Date('Y-m-d');

        $different = date_diff(date_create($now), date_create($accountReceivable->due_date_temp));

        if ($now > $accountReceivable->due_date_temp) {
            //cek apakah sudah data dari table over due
            OverDue::updateOrCreate([
                'business_id' => $business,
                'account_receivable_id' => $accountReceivable->id
            ], [
                'category' => $accountReceivable->category,
                'over_due' => $different->days,
                'description' => $different->y . ' Tahun ' . $different->m . ' Bulan ' . $different->d . ' Hari '
            ]);
            
        } else {
            $overDue = OverDue::whereBusinessId($business)
                                ->whereAccountReceivableId($accountReceivable->id)
                                ->first();
            if ($overDue) {
                $overDue->delete();
            }
        }
    }

    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        
        $identity = Identity::first();
        return view('business.over-due.index', compact('business','identity'));
    }

    public function getApi(Business $business)
    {
        $data = OverDue::whereBusinessId($business['id'])
                        ->with('accountReceivable')
                        ->orderBy('over_due', 'desc')
                        ->paginate(50);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function getSingleApi(Business $business, $id)
    {
        $data = OverDue::find($id);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function writeOff(Request $request, Business $business, $id)
    {
        $data = OverDue::find($id);
        $data->delete();

        $accountReceivable = AccountReceivable::find($data['account_receivable_id']);

        $accountReceivable->update([
            'is_paid_off' => true
        ]);

        $accountReceivables = AccountReceivable::whereBusinessId($business['id'])
                                                ->whereInvoiceId($accountReceivable['invoice_id'])
                                                ->get();

        $diffAccountReceivable = $accountReceivables->sum('debit') - $accountReceivables->sum('credit');

        $fix_ref_no = '';
        $date = date('Ymd');

        $endAccountReceivablePayment = AccountReceivablePayment::where('business_id', $business['id'])
                            ->where('no_ref', 'like', 'ARWO-' . $date . '%')
                            ->orderBy('id', 'desc')
                            ->first();

        $newAccountReceivable = 'ARWO-' . $date . '0001';

        if ($endAccountReceivablePayment) {
            $split_end_invoice = explode('-', $endAccountReceivablePayment['no_ref']);

            $newNumber = (int)$split_end_invoice[1] + 1;

            $newAccountReceivable = 'ARWO-' . $newNumber;
        }

        //tambah ke table pembayaran piutang (account receivable payment)
        $attributes['no_ref'] = $newAccountReceivable;
        $attributes['author'] = $request->user()->name;
        $attributes['contact_id'] = $accountReceivable['contact_id'];
        $attributes['contact_name'] = $accountReceivable['contact_name'];
        $attributes['invoice_id'] = $accountReceivable['invoice_id'];
        $attributes['value'] = $diffAccountReceivable;
        $attributes['credit'] = $diffAccountReceivable;
        $attributes['debit'] = 0;
        $attributes['business_id'] = $business['id'];
        $attributes['description'] = 'Penghapusan Piutang';
        $attributes['desc'] = 'Penghapusan Piutang';
        $attributes['type'] = 'Penghapusan Piutang';
        $attributes['source'] = 'Dari Penghapusan Piutang';
        $attributes['category'] = $accountReceivable['category'];
        $attributes['account_receivable_id'] = $accountReceivable['id'];
        $attributes['date'] = date('Y-m-d');
        $attributes['is_write_off'] = true;
        $attributes['is_paid_off'] = true;

        AccountReceivablePayment::create($attributes);
        AccountReceivable::create($attributes);

        Businessjournal::create($attributes);

        //buku besar
        //posisi credit
        //buku besar pada credit
        $account_name = 'Piutang Dagang';
        
        if ($accountReceivable['category'] == 'lend') {
            $account_name = 'Piutang Nasabah Simpan Pinjam';
        }

        $account = Businessaccount::whereBusinessId($business['id'])
                                    ->whereName($account_name)
                                    ->first();

        $attributes['account_id'] = $account['id'];
        $attributes['account_code'] = $account['code'];
        $attributes['account_name'] = $account['name'];
        Businessledger::create($attributes);

        //posisi debit
        $account_name = 'Beban Piutang';

        $account = Businessaccount::whereBusinessId($business['id'])
                                    ->whereName($account_name)
                                    ->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_code'] = $account['code'];
        $attributes['account_name'] = $account['name'];

        $attributes['debit'] = $attributes['value'];
        $attributes['credit'] = 0;  

        Businessledger::create($attributes);
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function update(Business $business)
    {
        // ambil data pada tabel piutang dimana nilai kolom is_paid_off == false
        $accountReceivables = AccountReceivable::whereBusinessId($business['id'])
                                                    ->where('debit', '>', 0)
                                                    ->whereIsPaidOff(false)
                                                    ->orderBy('date')
                                                    ->get();
        
        foreach ($accountReceivables as $accountReceivable) {
            $this->updateOrCreate($business['id'], $accountReceivable);
        }

        return response()->json([
            'status' => 'success',
            'data' => OverDue::whereBusinessId($business['id'])
                    ->with('accountReceivable')
                    ->orderBy('over_due', 'desc')
                    ->paginate(50)
        ]);
        
    }
}
