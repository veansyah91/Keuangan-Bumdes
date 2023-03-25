<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\AccountPayable;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Models\Businessjournal;
use App\Models\Businesscashflow;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountPayablePayment;

class AccountPayablePaymentController extends Controller
{
    public function newNoRefInvoice($no_ref_request, $no_ref_invoice){
        $split_invoice_ref_no = explode("-", $no_ref_invoice);
        $old_ref_no = (int)$split_invoice_ref_no[1];
        $new_ref_no = 1000000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }

    public function noRefAccountPayablePaymentRecomendation(Business $business){
        $fix_ref_no = '';
        $date = date('Ymd');

        $endAccountPayablePayment = AccountPayablePayment::where('business_id', $business['id'])
                            ->where('no_ref', 'like', 'APP-' . $date . '%')
                            ->orderBy('id', 'desc')
                            ->first();

        $newAccountPayable = 'APP-' . $date . '0001';

        if ($endAccountPayablePayment) {
            $split_end_invoice = explode('-', $endAccountPayablePayment['no_ref']);

            $newNumber = (int)$split_end_invoice[1] + 1;

            $newAccountPayable = 'APP-' . $newNumber;
        }

        return response()->json([
            'status' => 'success',
            'data' => $newAccountPayable,
        ]);
    }
    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        
        $identity = Identity::first();
        return view('business.account-payable-payment.index', compact('business','identity'));
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
        $attributes['purchase_goods_id'] = $request->invoice['id'];
        $attributes['debit'] = $attributes['value'];
        $attributes['credit'] = 0;
        $attributes['business_id'] = $business['id'];
        $attributes['desc'] = $attributes['description'];
        $attributes['type'] = 'Pembayaran Utang';
        $attributes['source'] = 'Dari Faktur Pembayaran Utang';

        $payment = AccountPayablePayment::create($attributes);
        $accountPayable = AccountPayable::create($attributes);
        $journal = Businessjournal::create($attributes);
        
        //buku besar pada credit
        $account = Businessaccount::where('business_id', $business['id'])->where('name', 'Utang Usaha')->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_code'] = $account['code'];
        $attributes['account_name'] = $account['name'];
        Businessledger::create($attributes);

        //cashflow
        $attributes['type'] = 'operation';
        $attributes['credit'] = $attributes['value'];
        $attributes['debit'] = 0;
        $cashFlow = Businesscashflow::create($attributes);

        //debit
        $account = Businessaccount::where('business_id', $business['id'])->where('id', $request->credit['id'])->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_code'] = $account['code'];
        $attributes['account_name'] = $account['name'];
        
        $journal = Businessledger::create($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    public function show(Business $business, $id)
    {
        $accountPayablePayment = AccountPayablePayment::find($id);
        $accountPayable = AccountPayable::where('business_id', $business['id'])->where('no_ref', $accountPayablePayment['no_ref'])->with('purchaseGoods')->with('contact')->first();

        $accountPayablePayment['purchaseGoods'] = [
            "id" => $accountPayable['purchaseGoods']['id'],
            "no_ref" => $accountPayable['purchaseGoods']['no_ref'],
            "date" => $accountPayable['purchaseGoods']['date'],
        ];
        $accountPayablePayment['contact'] = [
            "id" => $accountPayable['contact']['id'],
            "name" => $accountPayable['contact']['name'],
        ];

        $ledger = Businessledger::where('no_ref', $accountPayablePayment['no_ref'])
                                ->where('business_id', $business['id'])
                                ->where('credit', '>', 0)
                                ->first();

        $accountPayablePayment['credit'] = [
                                    "id" => $ledger['account_id'],
                                    "name" => $ledger['account_name'],
                                ];         
        $accountPayablePayment['description'] = $ledger['description'];       
        
        return response()->json([
            'status' => 'success',
            'data' => $accountPayablePayment,
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
        $attributes['purchase_goods_id'] = $request->invoice['id'];
        $attributes['debit'] = $attributes['value'];
        $attributes['credit'] = 0;
        $attributes['business_id'] = $business['id'];
        $attributes['desc'] = $attributes['description'];
        $attributes['type'] = 'Pembayaran Utang';
        $attributes['source'] = 'Dari Faktur Pembayaran Utang';

        $accountPayablePayment = AccountPayablePayment::find($id);

        //update data pada account receivable
        $accountPayable = AccountPayable::where('business_id', $business['id'])->where('no_ref', $accountPayablePayment['no_ref'])->first();
        $accountPayable->update($attributes);

        //update data pada journal
        $journal = Businessjournal::where('business_id', $business['id'])->where('no_ref', $accountPayablePayment['no_ref'])->first();
        $journal->update($attributes);

        //buku besar pada credit
        $account = Businessaccount::where('business_id', $business['id'])->where('name', 'Utang Usaha')->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_code'] = $account['code'];
        $attributes['account_name'] = $account['name'];

        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $accountPayablePayment['no_ref'])->where('debit', '>', 0)->first();
        $ledgers->update($attributes);

        //cashflow
        $attributes['type'] = 'operation';
        $attributes['credit'] = $attributes['value'];
        $attributes['debit'] = 0;

        $cashFlow = Businesscashflow::where('business_id', $business['id'])->where('no_ref', $accountPayablePayment['no_ref'])->first();
        $cashFlow->update($attributes);

        //buku besar pada debit
        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $accountPayablePayment['no_ref'])->where('credit', '>', 0)->first();
        $account = Businessaccount::where('business_id', $business['id'])->where('id', $request->credit['id'])->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_code'] = $account['code'];
        $attributes['account_name'] = $account['name'];
        
        $ledgers->update($attributes);

        //ubah account receivable payment table
        $accountPayablePayment->update($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $accountPayablePayment,
        ]);
    }

    public function destroy(Business $business, $id)
    {
        //hapus data pada account receivable payment
        $accountPayablePayment = AccountPayablePayment::find($id);
        $no_ref = $accountPayablePayment['no_ref'];
        $accountPayablePayment->delete();

        //hapus data pada account receivable
        $accountPayable = AccountPayable::where('business_id', $business['id'])->where('no_ref', $no_ref)->first();
        $accountPayable->delete();

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
            'data' => $accountPayablePayment,
        ]);
    }

    public function getApi(Business $business, Request $request)
    {
        $data = AccountPayablePayment::where('business_id', $business['id'])
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
        $accountPayablePayment = AccountPayablePayment::where('id', $id)
                    ->first();
        $accountPayable = AccountPayable::where('business_id', $business['id'])
                                                ->with('purchaseGoods')
                                                ->where('no_ref', $accountPayablePayment['no_ref'])
                                                ->first();

        $accountPayables = AccountPayable::where('business_id', $business['id'])
                                            ->where('purchase_goods_id', $accountPayable['purchase_goods_id'])
                                            ->get();

        return view('business.account-payable-payment.print-detail', compact('business', 'accountPayablePayment', 'accountPayable', 'accountPayables', 'identity'));
    }
}
