<?php

namespace App\Http\Controllers\Business;

use App\Models\Invoice;
use App\Models\Business;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\DebtSubmission;
use App\Models\Businessaccount;
use App\Models\Businessjournal;
use App\Models\Businesscashflow;
use App\Models\AccountReceivable;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LendController extends Controller
{
    public function noRefLendRecomendation(Business $business){
        $fix_ref_no = '';

        $date = str_replace('-', '', request('date'));

        $endLend = Invoice::where('business_id', $business['id'])
                            ->where('no_ref', 'like', 'L-'. $date . '%')
                            ->orderBy('id', 'desc')
                            ->first();

        $newAccountPayable = 'L-' . $date . '0001';

        if ($endLend) {
            $split_end_invoice = explode('-', $endLend['no_ref']);

            $newNumber = (int)$split_end_invoice[1] + 1;

            $newAccountPayable = 'L-' . $newNumber;
        }

        return response()->json([
            'status' => 'success',
            'data' => $newAccountPayable,
        ]);
    }

    public function getData(Business $business)
    {
        return response()->json([
            'status' => 'success',
            'data' => AccountReceivable::filter(request(['date_from','date_to','this_week','this_month','this_year', 'search']))
                                ->whereBusinessId($business['id'])
                                ->where('debit', '>', 0)
                                ->whereCategory('lend')
                                ->with('contact')
                                ->orderBy('date', 'desc')
                                ->latest()
                                ->paginate(50),
        ]);
    }

    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }

        $identity = Identity::first();

        return view('business.lend.index', ['business' => $business, 'identity' => $identity, ]);
    }

    public function store(Business $business, Request $request)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'value' => 'required|numeric',
            'tenor' => 'required|numeric|min:1',
        ]); 

        if (!$request->contact['id']) {
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        if ($request->debt_submission['id']) {
            $attributes['debt_summary_id'] = $request->debt_submission['id'];

            $debtSubmission = DebtSubmission::find($attributes['debt_summary_id']);
            
            $debtSubmission->update([
                'status' => 'approved',
                'tenor' => $attributes['tenor']
            ]);
            $attributes['debt_submission_id'] = $debtSubmission['id'];
        }

        $attributes['contact_id'] = $request->contact['id'];
        $attributes['business_id'] = $request->business['id'];
        $attributes['contact_name'] = $request->contact['name'];
        $attributes['debit'] = $attributes['value'];
        $attributes['credit'] = 0;
        $attributes['category'] = 'lend';
        $attributes['description'] = 'Pemberian Pinjaman Kepada ' . $attributes['contact_name'];
        $attributes['desc'] = 'Pemberian Pinjaman Kepada ' . $attributes['contact_name'];
        $attributes['due_date_temp'] = date('Y-m-d', strtotime('+1 month', strtotime($attributes['date'])));
        
        $attributes['author'] = $request->user()->name;

        $invoice = Invoice::create($attributes);

        $attributes['invoice_id'] = $invoice['id'];

        $debtSubmission = AccountReceivable::create($attributes);

        //ledger
        //akun debit => piutang
        $account = Businessaccount::where('business_id', $business['id'])
                                    ->where('name', 'Piutang Nasabah Simpan Pinjam')
                                    ->first();

        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];

        Businessledger::create($attributes);

        $attributes['credit'] = $attributes['value'];
        $attributes['debit'] = 0;
        $attributes['type'] = 'operation';
        
        //cashflow
        Businesscashflow::create($attributes);

        //akun credit => pada kas
        $account = Businessaccount::find($request->account['id']);
        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];

        Businessledger::create($attributes);
        
        $attributes['source'] = 'Pinjaman Nasabah';

        //journal
        Businessjournal::create($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    public function show(Business $business, $id)
    {
        $data = AccountReceivable::whereId($id)->with('contact')->first();
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function update(Request $request, Business $business, $id)
    {
         // validasi input revenue 
         $attributes = $request->validate([
            'no_ref' => 'required',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'value' => 'required|numeric',
            'tenor' => 'required|numeric|min:1',
        ]); 

        if (!$request->contact['id']) {
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        $accountReceivable = AccountReceivable::find($id);

        if ($accountReceivable['debt_submission_id']) {
            $debtSubmission = DebtSubmission::find($accountReceivable['debt_submission_id']);

            $debtSubmission->update([
                'status' => 'pending'
            ]);
        }

        $attributes['debt_submission_id'] = null;

        if ($request->debt_submission['no_ref']) {
            $attributes['debt_summary_id'] = $request->debt_submission['id'];

            $debtSubmission = DebtSubmission::find($attributes['debt_summary_id']);
            
            $debtSubmission->update([
                'status' => 'approved',
                'tenor' => $attributes['tenor']
            ]);
            $attributes['debt_submission_id'] = $debtSubmission['id'];
        }

        $attributes['contact_id'] = $request->contact['id'];
        $attributes['business_id'] = $request->business['id'];
        $attributes['contact_name'] = $request->contact['name'];
        $attributes['debit'] = $attributes['value'];
        $attributes['credit'] = 0;
        $attributes['category'] = 'lend';
        $attributes['description'] = 'Pemberian Pinjaman Kepada ' . $attributes['contact_name'];
        $attributes['desc'] = 'Pemberian Pinjaman Kepada ' . $attributes['contact_name'];
        $attributes['due_date_temp'] = date('Y-m-d', strtotime('+1 month', strtotime($attributes['date'])));
        $attributes['author'] = $request->user()->name;
        
        //ledger
        //hapus ledger
        $ledgers = Businessledger::whereBusinessId($business['id'])
                                ->whereNoRef($accountReceivable['no_ref'])
                                ->get();

        if (count($ledgers) > 0) {
            foreach ($ledgers as $ledger) {
                $ledger->delete();
            }
        }        

        $accountReceivable->update($attributes);      
        $invoice = Invoice::findOrFail($accountReceivable['invoice_id']);
        $invoice->update($attributes);

        //akun debit => piutang
        $account = Businessaccount::where('business_id', $business['id'])
                                    ->where('name', 'Piutang Nasabah Simpan Pinjam')
                                    ->first();

        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];

        Businessledger::create($attributes);

        $attributes['credit'] = $attributes['value'];
        $attributes['debit'] = 0;
        $attributes['type'] = 'operation';
        
        //cashflow
        $cashflow = Businesscashflow::whereBusinessId($business['id'])
                                    ->whereNoRef($accountReceivable['no_ref'])  
                                    ->first();

        $cashflow->update($attributes);

        //akun credit => pada kas
        $account = Businessaccount::find($request->account['id']);
        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];

        Businessledger::create($attributes);
        
        $attributes['source'] = 'Pinjaman Nasabah';

        //journal
        $journal = Businessjournal::whereBusinessId($business['id'])
                                    ->whereNoRef($accountReceivable['no_ref'])  
                                    ->first();
        $journal->create($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    public function destroy(Business $business, $id)
    {
        $data = AccountReceivable::findOrFail($id);

        $accountReceivables = AccountReceivable::whereBusinessId($business['id'])
                            ->where('invoice_id', $data['invoice_id'])
                            ->get();

        if (count($accountReceivables) > 1) {
            throw ValidationException::withMessages([
                'message' => 'Data Tidak Bisa Dihapus, Data Telah Digunakan'
            ]);
        }

        if ($data['debt_submission_id']) {
            $debtSubmission = DebtSubmission::find($data['debt_submission_id']);

            $debtSubmission->update([
                'status' => 'pending'
            ]);
        }

        $journals = Businessjournal::whereBusinessId($business['id'])
                                    ->whereNoRef($data['no_ref'])  
                                    ->get();

        if (count($journals) > 0) {
            foreach ($journals as $journal) {
                $journal->delete();
            }
        }

        $ledgers = Businessledger::whereBusinessId($business['id'])
                                ->whereNoRef($data['no_ref'])  
                                ->get();

        if (count($ledgers) > 0) {
            foreach ($ledgers as $ledger) {
                $ledger->delete();
            }
        }

        $cashflows = Businesscashflow::whereBusinessId($business['id'])
                                ->whereNoRef($data['no_ref'])  
                                ->get();

        if (count($cashflows) > 0) {
            foreach ($cashflows as $cashflow) {
                $cashflow->delete();
            }
        }

        $data->delete();

        $invoice = Invoice::findOrFail($data['invoice_id']);

        $invoice->delete();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function card(Business $business, $id)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        $identity = Identity::first();

        $accountReceivable = AccountReceivable::find($id);

        $payments = AccountReceivable::whereBusinessId($business['id'])   
                                        ->whereInvoiceId($accountReceivable['invoice_id'])
                                        ->where('credit', '>', 0)
                                        ->whereCategory('lend')
                                        ->with('contact')
                                        ->get();


        return view('business.lend.card', [
            'business' => $business, 
            'accountReceivable' => $accountReceivable, 
            'payments' => $payments, 
            'identity' => $identity, 
        ]);
    }
}
