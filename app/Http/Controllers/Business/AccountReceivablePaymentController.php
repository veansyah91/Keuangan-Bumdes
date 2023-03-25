<?php

namespace App\Http\Controllers\Business;

use App\Models\Account;
use App\Models\Business;
use App\Models\Cashflow;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Models\Businessjournal;
use App\Models\Businesscashflow;
use App\Models\AccountReceivable;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountReceivablePayment;
use App\Http\Controllers\Business\OverDueController;

class AccountReceivablePaymentController extends Controller
{
    public function newNoRefInvoice($no_ref_request, $no_ref_invoice){
        $split_invoice_ref_no = explode("-", $no_ref_invoice);
        $old_ref_no = (int)$split_invoice_ref_no[1];
        $new_ref_no = 1000000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }

    public function noRefAccountReceivablePaymentRecomendation(Business $business){
        $fix_ref_no = '';
        $date = date('Ymd');

        $endAccountReceivablePayment = AccountReceivablePayment::where('business_id', $business['id'])
                            ->where('no_ref', 'like', 'ARP-' . $date . '%')
                            ->orderBy('id', 'desc')
                            ->first();

        $newAccountReceivable = 'ARP-' . $date . '0001';

        if ($endAccountReceivablePayment) {
            $split_end_invoice = explode('-', $endAccountReceivablePayment['no_ref']);

            $newNumber = (int)$split_end_invoice[1] + 1;

            $newAccountReceivable = 'ARP-' . $newNumber;
        }

        return response()->json([
            'status' => 'success',
            'data' => $newAccountReceivable,
        ]);
    }
    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        
        $identity = Identity::first();
        return view('business.account-receivable-payment.index', compact('business','identity'));
    }

    public function store(Business $business, Request $request)
    {
        $attributes = $request->validate([
            'value' => 'numeric|required',
            'no_ref' => 'required',
            'date' => 'required',
            'description' => 'string|nullable',
        ]);

        $attributes['author'] = $request->user()->name;
        $attributes['contact_id'] = $request->contact['id'];
        $attributes['contact_name'] = $request->contact['name'];
        $attributes['invoice_id'] = $request->invoice['id'];
        $attributes['credit'] = $attributes['value'];
        $attributes['debit'] = 0;
        $attributes['business_id'] = $business['id'];
        $attributes['desc'] = $attributes['description'];
        $attributes['type'] = 'Pembayaran Piutang';
        $attributes['source'] = 'Dari Faktur Pembayaran Piutang';

        //cek jenis piutang
        $checkAccountReceivable = AccountReceivable::whereInvoiceId($attributes['invoice_id'])
                                                    ->where('business_id', $business['id'])
                                                    ->where('debit', '>', 0)
                                                    ->first();
        
        if ($checkAccountReceivable['category'] == 'lend') {
            $attributes['category'] = 'lend';
        }

        if ($checkAccountReceivable['category'] == 'credit') {
            $attributes['category'] = 'credit';
        }

        //buku besar pada credit
        $account_name = 'Piutang Dagang';
        
        if ($checkAccountReceivable['category'] == 'lend') {
            $account_name = 'Piutang Nasabah Simpan Pinjam';
        }

        $accountReceivable = AccountReceivable::create($attributes);

        $attributes['account_receivable_id'] = $accountReceivable['id'];

        $accountReceivablePayment = AccountReceivablePayment::create($attributes);
        $journal = Businessjournal::create($attributes);

        $account = Businessaccount::where('business_id', $business['id'])->where('name', $account_name)->first();

        $attributes['account_id'] = $account['id'];
        $attributes['account_code'] = $account['code'];
        $attributes['account_name'] = $account['name'];
        Businessledger::create($attributes);
        $attributes['type'] = 'operation';
        $attributes['debit'] = $attributes['value'];
        $attributes['credit'] = 0;
        $cashFlow = Businesscashflow::create($attributes);

        //debit
        $account = Businessaccount::where('business_id', $business['id'])->where('id', $request->debit['id'])->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_code'] = $account['code'];
        $attributes['account_name'] = $account['name'];
        
        $journal = Businessledger::create($attributes);

        //update status piutang jika lunas
        $accountReceivables = AccountReceivable::whereBusinessId($business['id'])
                                                ->whereInvoiceId($attributes['invoice_id'])
                                                ->get();

        //dapatkan total debit dan kredit
        $total_debit = $accountReceivables->sum('debit');
        $total_credit = $accountReceivables->sum('credit');

        if ($total_debit - $total_credit == 0) {
            foreach ($accountReceivables as $accountReceivable) {
                $accountReceivable->update([
                    'is_paid_off' => true,
                    'due_date_temp' => ''
                ]);
            }
        } else {
            foreach ($accountReceivables as $accountReceivable) {
                $accountReceivable->update([
                    'is_paid_off' => false
                ]);
            }
        }
                                
        //hitung berapa bulan terbayar berdasarkan jumlah pembayaran
        $month = floor($total_credit / round($total_debit / $checkAccountReceivable['tenor'])) + 1;
        $next_month = '+' . $month . 'month';
        $attributes['due_date_temp'] = date('Y-m-d', strtotime($next_month, strtotime($checkAccountReceivable['date'])));

        $checkAccountReceivable->update([
            'due_date_temp' => $attributes['due_date_temp']
        ]);

         // jika piutang lunas, maka hapus data piutang 
         if ($checkAccountReceivable['is_paid_off']) {
            $overDue = OverDue::whereBusinessId($business['id'])
                                ->whereAccountReceivableId($checkAccountReceivable['id'])
                                ->first();
            if ($overDue) {
                $overDue->delete();
            }
        } else {
            OverDueController::updateOrCreate($business['id'], $checkAccountReceivable);
        }

        return response()->json([
            'status' => 'success',
            'data' => $accountReceivablePayment,
        ]);
    }

    public function show(Business $business, $id)
    {
        $accountReceivablePayment = AccountReceivablePayment::find($id);
        $accountReceivable = AccountReceivable::where('business_id', $business['id'])
                                            ->where('no_ref', $accountReceivablePayment['no_ref'])
                                            ->with('invoice', fn($query) => 
                                                $query->with('accountReceivables')
                                                        ->with('products')
                                                        ->withSum('accountReceivables', 'debit')
                                                        ->withSum('accountReceivables', 'credit')
                                            )
                                            ->with('contact')
                                            ->first();

        $accountReceivablePayment['accountReceivable'] = $accountReceivable;

        $ledger = Businessledger::where('no_ref', $accountReceivablePayment['no_ref'])
                                ->where('business_id', $business['id'])
                                ->where('debit', '>', 0)
                                ->first();

        $accountReceivablePayment['debit'] = [
                                    "id" => $ledger['account_id'],
                                    "name" => $ledger['account_name'],
                                ];         
        $accountReceivablePayment['description'] = $ledger['description'];       
        
        return response()->json([
            'status' => 'success',
            'data' => $accountReceivablePayment,
        ]);
    }

    public function update(Business $business, $id, Request $request)
    {
        $attributes = $request->validate([
            'value' => 'numeric|required',
            'no_ref' => 'required',
            'date' => 'required',
            'description' => 'string|nullable',
        ]);
        
        $attributes['author'] = $request->user()->name;
        $attributes['contact_id'] = $request->contact['id'];
        $attributes['contact_name'] = $request->contact['name'];
        $attributes['invoice_id'] = $request->invoice['id'];
        $attributes['credit'] = $attributes['value'];
        $attributes['debit'] = 0;
        $attributes['business_id'] = $business['id'];
        $attributes['desc'] = $attributes['description'];
        $attributes['type'] = 'Pembayaran Piutang';
        $attributes['source'] = 'Dari Faktur Pembayaran Piutang';

        $accountReceivablePayment = AccountReceivablePayment::find($id);

        //hapus data pada account receivable
        $accountReceivable = AccountReceivable::where('business_id', $business['id'])->where('no_ref', $accountReceivablePayment['no_ref'])->first();
        $accountReceivable->update($attributes);

        //hapus data pada journal
        $journal = Businessjournal::where('business_id', $business['id'])->where('no_ref', $accountReceivablePayment['no_ref'])->first();
        $journal->update($attributes);

        //buku besar pada credit
        $account_name = 'Piutang Dagang';
        
        if ($accountReceivable['category'] == 'lend') {
            $account_name = 'Piutang Nasabah Simpan Pinjam';
        }

        //buku besar pada credit
        $account = Businessaccount::where('business_id', $business['id'])->where('name', $account_name)->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_code'] = $account['code'];
        $attributes['account_name'] = $account['name'];
        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $accountReceivablePayment['no_ref'])->where('credit', '>', 0)->first();
        $ledgers->update($attributes);

        $attributes['type'] = 'operation';
        $attributes['debit'] = $attributes['value'];
        $attributes['credit'] = 0;
        //cashflow
        $cashFlow = Businesscashflow::where('business_id', $business['id'])->where('no_ref', $accountReceivablePayment['no_ref'])->first();
        $cashFlow->update($attributes);

        //buku besar pada debit
        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $accountReceivablePayment['no_ref'])->where('debit', '>', 0)->first();
        $account = Businessaccount::where('business_id', $business['id'])->where('id', $request->debit['id'])->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_code'] = $account['code'];
        $attributes['account_name'] = $account['name'];
        
        $ledgers->update($attributes);

        //ubah account receivable payment table
        $accountReceivablePayment->update($attributes);

        //update status piutang jika lunas
        $accountReceivables = AccountReceivable::whereBusinessId($business['id'])
                                                ->whereInvoiceId($attributes['invoice_id'])
                                                ->get();

        //dapatkan total debit dan kredit
        $total_debit = $accountReceivables->sum('debit');
        $total_credit = $accountReceivables->sum('credit');

        if ($total_debit - $total_credit == 0) {
            foreach ($accountReceivables as $accountReceivable) {
                $accountReceivable->update([
                    'is_paid_off' => true
                ]);
            }
        } else {
            foreach ($accountReceivables as $accountReceivable) {
                $accountReceivable->update([
                    'is_paid_off' => false
                ]);
            }
        }

        //cek jenis piutang
        $checkAccountReceivable = AccountReceivable::whereInvoiceId($attributes['invoice_id'])
                                                    ->where('business_id', $business['id'])
                                                    ->where('debit', '>', 0)
                                                    ->first();

        //hitung berapa bulan terbayar berdasarkan jumlah pembayaran
        $month = floor($total_credit / round($total_debit / $checkAccountReceivable['tenor'])) + 1;
        $next_month = '+' . $month . 'month';
        $attributes['due_date_temp'] = date('Y-m-d', strtotime($next_month, strtotime($checkAccountReceivable['date'])));

        $checkAccountReceivable->update([
            'due_date_temp' => $attributes['due_date_temp']
        ]);

        // jika piutang lunas, maka hapus data piutang 
        if ($checkAccountReceivable['is_paid_off']) {
            $overDue = OverDue::whereBusinessId($business['id'])
                                ->whereAccountReceivableId($checkAccountReceivable['id'])
                                ->first();
            if ($overDue) {
                $overDue->delete();
            }
        } else {
            OverDueController::updateOrCreate($business['id'], $checkAccountReceivable);
        }

        return response()->json([
            'status' => 'success',
            'data' => $accountReceivablePayment,
        ]);
    }

    public function destroy(Business $business, $id)
    {
        //hapus data pada account receivable payment
        $accountReceivablePayment = AccountReceivablePayment::find($id);
        $no_ref = $accountReceivablePayment['no_ref'];
        $accountReceivablePayment->delete();

        //hapus data pada account receivable
        $accountReceivable = AccountReceivable::where('business_id', $business['id'])->where('no_ref', $no_ref)->first();
        $accountReceivable->delete();

        $accountReceivables = AccountReceivable::where('business_id', $business['id'])
                                                ->whereInvoiceId($accountReceivable['invoice_id'])
                                                ->get();

        if (count($accountReceivables) > 0) {
            foreach ($accountReceivables as $accountReceivable) {
                $accountReceivable->update([
                    'is_paid_off' => false
                ]);
            }
        }

        //dapatkan total debit dan kredit
        $total_debit = $accountReceivables->sum('debit');
        $total_credit = $accountReceivables->sum('credit');

        //cek jenis piutang
        $checkAccountReceivable = AccountReceivable::whereInvoiceId($accountReceivable['invoice_id'])
                                                    ->where('business_id', $business['id'])
                                                    ->where('debit', '>', 0)
                                                    ->first();

        //hitung berapa bulan terbayar berdasarkan jumlah pembayaran
        $month = floor($total_credit / round($total_debit / $checkAccountReceivable['tenor'])) + 1;
        $next_month = '+' . $month . 'month';
        $attributes['due_date_temp'] = date('Y-m-d', strtotime($next_month, strtotime($checkAccountReceivable['date'])));

        $checkAccountReceivable->update([
            'due_date_temp' => $attributes['due_date_temp']
        ]);

        //hapus data pada journal
        $journal = Businessjournal::where('business_id', $business['id'])->where('no_ref', $no_ref)->first();
        $journal->delete();

        //hapus data pada cashflow
        $cashflow = Businesscashflow::where('business_id', $business['id'])->where('no_ref', $no_ref)->first();

        if ($cashflow) {
            $cashflow->delete();
        }

        //hapus data pada buku besar
        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $no_ref)->get();
        foreach ($ledgers as $ledger) {
            $ledger->delete();
        }

        return response()->json([
            'status' => 'success',
            'data' => $accountReceivablePayment,
        ]);
    }

    public function getApi(Business $business, Request $request)
    {
        $data = AccountReceivablePayment::where('business_id', $business['id'])
                                        ->filter(request(['search','date_from','date_to','this_week','this_month','this_year']))
                                        ->with('contact', function ($query) use ($request){
                                            $query->orWhere('name', 'like', '%' . $request->search . '%');
                                        })
                                        ->orderBy('date', 'desc')
                                        ->orderBy('id', 'desc')
                                        ->paginate(50);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function printDetail(Business $business, $id, Request $request)
    {
        try {
            $identity = Identity::first();
            $accountReceivablePayment = AccountReceivablePayment::where('id', $id)
                        ->first();

            $accountReceivable = AccountReceivable::where('business_id', $business['id'])
                                                    ->with('invoice')
                                                    ->where('no_ref', $accountReceivablePayment['no_ref'])
                                                    ->first();

            $accountReceivables = AccountReceivable::where('business_id', $business['id'])
                                                ->where('invoice_id', $accountReceivable['invoice_id'])
                                                ->get();

            return view('business.account-receivable-payment.print-detail', compact('business', 'accountReceivablePayment', 'accountReceivable', 'accountReceivables', 'identity'));
    
        } catch (\Throwable $th) {
            return abort(402);
        }
         
    }
}
