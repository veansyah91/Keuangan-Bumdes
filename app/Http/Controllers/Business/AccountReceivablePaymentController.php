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

        $payment = AccountReceivablePayment::create($attributes);
        $accountReceivable = AccountReceivable::create($attributes);
        $journal = Businessjournal::create($attributes);
        
        //buku besar pada credit
        $account = Businessaccount::where('business_id', $business['id'])->where('name', 'Piutang Dagang')->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_code'] = $account['code'];
        $attributes['account_name'] = $account['name'];
        Businessledger::create($attributes);

        //debit
        $account = Businessaccount::where('business_id', $business['id'])->where('id', $request->debit['id'])->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_code'] = $account['code'];
        $attributes['account_name'] = $account['name'];
        $attributes['type'] = 'operation';
        $attributes['debit'] = $attributes['value'];
        $attributes['credit'] = 0;
        $cashFlow = Businesscashflow::create($attributes);
        $journal = Businessledger::create($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    public function show(Business $business, $id)
    {
        $accountReceivablePayment = AccountReceivablePayment::find($id);
        $accountReceivable = AccountReceivable::where('business_id', $business['id'])->where('no_ref', $accountReceivablePayment['no_ref'])->with('invoice')->with('contact')->first();
        $accountReceivablePayment['invoice'] = [
            "id" => $accountReceivable['invoice']['id'],
            "no_ref" => $accountReceivable['invoice']['no_ref'],
            "date" => $accountReceivable['invoice']['date'],
        ];
        $accountReceivablePayment['contact'] = [
            "id" => $accountReceivable['contact']['id'],
            "name" => $accountReceivable['contact']['name'],
        ];

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
        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $accountReceivablePayment['no_ref'])->where('credit', '>', 0)->first();
        $ledgers->update($attributes);

        //buku besar pada debit
        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $accountReceivablePayment['no_ref'])->where('debit', '>', 0)->first();
        $account = Businessaccount::where('business_id', $business['id'])->where('id', $request->debit['id'])->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_code'] = $account['code'];
        $attributes['account_name'] = $account['name'];
        $attributes['type'] = 'operation';
        $attributes['debit'] = $attributes['value'];
        $attributes['credit'] = 0;
        $ledgers->update($attributes);

        //cashflow
        $cashFlow = Businesscashflow::where('business_id', $business['id'])->where('no_ref', $accountReceivablePayment['no_ref'])->first();
        $cashFlow->update($attributes);

        //ubah account receivable payment table
        $accountReceivablePayment->update($attributes);

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

        //hapus data pada journal
        $journal = Businessjournal::where('business_id', $business['id'])->where('no_ref', $no_ref)->first();
        $journal->delete();

        //hapus data pada cashflow
        $cashflow = Businesscashflow::where('business_id', $business['id'])->where('no_ref', $no_ref)->first();
        $cashflow->delete();

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
    }
}
