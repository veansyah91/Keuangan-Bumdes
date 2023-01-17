<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Models\Businessexpense;
use App\Models\Businessjournal;
use App\Models\Businesscashflow;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BusinessExpenseController extends Controller
{
    public function storeToCashFlow($request, $business)
    {
        foreach ($request->listInput as $list) {
            $account = Businessaccount::find($list['accountId']);

            $type = 'operation';

            $temp =  substr($account['code'], 0 , 2);
                
            if ((int)$temp > 15 && (int)$temp < 20) {
                $type = 'investment';
            }

            if ((int)$temp > 29 && (int)$temp < 40) {
                $type = 'finance';
            }

            Businesscashflow::create([
                'account_id' => $account['id'],
                'no_ref' => $request->no_ref,
                'date' => $request->date,
                'account_code' => $account['code'],
                'account_name' => $account['name'],
                'type' => $type,
                'debit' => 0,
                'credit' => $list['total'],
                'business_id' => $business['id']
            ]);
        }
    }

    public function storeToJournal($request, $business)
    {
        Businessjournal::create([
            'no_ref' => $request->no_ref,
            'desc' => $request->desc,
            'value' => $request->value,
            'detail' => $request->detail,
            'date' => $request->date,
            'author' =>   $request->user()->name,
            'source' => "Dari Pengeluaran",
            'business_id' => $business['id']
        ]);
    }

    public function storeToLedger($request, $business)
    {
        //debit: from_account
        $account = Businessaccount::find($request->from_account['id']);
        Businessledger::create([
            'account_name' => $account['name'],
            'account_id' => $account['id'],
            'debit' =>  0,
            'credit' => $request->value,
            'no_ref' => $request->no_ref,
            'date' => $request->date,
            'description' => $request->desc,
            'account_code' => $account['code'],
            'author' =>$request->user()->name,
            'note' => $request->detail,
            'business_id' => $business['id']
        ]);

        // credit : list 
        foreach ($request->listInput as $list) {
            $account = Businessaccount::find($list['accountId']);
            Businessledger::create([
                'account_name' => $account['name'],
                'account_id' => $account['id'],
                'debit' =>  $list['total'],
                'credit' => 0,
                'no_ref' => $request->no_ref,
                'date' => $request->date,
                'description' => $request->desc,
                'account_code' => $account['code'],
                'author' =>$request->user()->name,
                'note' => $request->detail,
                'business_id' => $business['id']
            ]);
        }
    }

    public function destroyJournal($no_ref, $business)
    {
        Businessjournal::where('business_id', $business['id'])->where('no_ref', $no_ref)->first()->delete();
    }

    public function destroyCashflow($no_ref, $business)
    {
        $cashflows = Businesscashflow::where('business_id', $business['id'])->where('no_ref', $no_ref)->get();

        foreach ($cashflows as $cashflow) {
            $cashflow->delete();
        }
    }

    public function destroyLedger($no_ref, $business)
    {
        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $no_ref)->get();

        foreach ($ledgers as $ledger) {
            $ledger->delete();
        }
    }

    public function newNoRefExpense($no_ref_request, $no_ref_expense){
        $split_expense_ref_no = explode("-", $no_ref_expense);
        $old_ref_no = (int)$split_expense_ref_no[1];
        $new_ref_no = 1000000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }

    public function noRefExpenseRecomendation(Business $business)
    {
        // $ref_no = explode("-", request('search'));
        $businessexpense = Businessexpense::where('business_id', $business['id'])->orderBy('id', 'desc')->first();

        $fix_ref_no = '';

        if($businessexpense){
            $fix_ref_no = $this->newNoRefExpense('CO', $businessexpense->no_ref);
        }else{
            $fix_ref_no = 'CO-000001';
        }

        return response()->json([
            'status' => 'success',
            'data' => $fix_ref_no,
        ]);
    }

    public function getApiData(Business $business){
        $data = Businessexpense::where('business_id', $business['id'])->filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50);
        
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.expense.index', compact('business'));
    }

    public function create(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.expense.create', compact('business'));
    }

    public function store(Request $request, Business $business)
    {
        // validasi input expense 
        $attributes = $request->validate([
            'no_ref' => 'required',
            'contact' => 'required',
            'desc' => 'required',
            'value' => 'required|numeric',
            'date' => 'date',
            'detail' => 'string|nullable',
        ]);

        //tambah data pada table expenses
        $businessexpense = Businessexpense::create([
            'no_ref' =>  $request->no_ref,
            'date' =>  $request->date,
            'description' =>  $request->desc,
            'value' =>  $request->value,
            'contact' =>  $request->contact,
            'author' =>   $request->user()->name,
            'detail' =>  $request->detail,
            'business_id' => $business['id']
        ]);

        //tambah data pada journal dengan mengisi source : Pengeluaran Dari: General Customer
        $this->storeToJournal($request, $business);

        //tambahkan ke arus kas
        //per masing-masing list pada debit
        $this->storeToCashFlow($request, $business);

        //tambahkan ke buku besar
        $this->storeToLedger($request, $business);

        return response()->json([
            'status' => 'success',
            'data' => $attributes
        ]);
    }

    public function printDetail(Business $business, Businessexpense $businessexpense){
        $businessexpense['date_format'] = Carbon::createFromDate($businessexpense->date)->toFormattedDateString();

        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $businessexpense->no_ref)->get();

        return view('business.expense.print-detail', compact('businessexpense', 'ledgers', 'business'));
    }

    public function print(Business $business){
        $businessexpenses = Businessexpense::where('business_id', $business['id'])->filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date', 'asc')->get();
       
        return view('business.expense.print', [
            'expenses' => $businessexpenses,
            'author' => request()->user(),
            'business' => $business
        ]);
    }

    public function show(Business $business, Businessexpense $businessexpense)
    {
        $businessexpense['created_at_for_human'] = $businessexpense->updated_at->diffForHumans();
        $businessexpense['is_updated'] = $businessexpense->updated_at != $businessexpense->created_at ? true : false;
        $businessexpense['ledgers'] = Businessledger::where('business_id', $business['id'])->where('no_ref', $businessexpense->no_ref)->get();

        return response()->json([
            'status' => 'success',
            'data' => $businessexpense,
        ]);
    }

    public function edit(Business $business, Businessexpense $businessexpense)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.expense.edit', [
            'expense_id' => $businessexpense['id'],
            'business' => $business
        ]);
    }

    public function update(Request $request, Business $business, Businessexpense $businessexpense)
    {
        // validasi input expense 
        $attributes = $request->validate([
            'no_ref' => 'required',
            'contact' => 'required',
            'desc' => 'required',
            'value' => 'required|numeric',
            'date' => 'date',
            'detail' => 'string|nullable',
        ]);

        //dapatkan data journal berdasarkan no_href
        $journal = Businessjournal::where('no_ref', $businessexpense['no_ref'])->first();

        //hapus data pada arus kas
        $this->destroyCashFlow($businessexpense['no_ref'], $business);

        //hapus data pada buku besar
        $this->destroyLedger($businessexpense['no_ref'], $business);

        //update
        // update expense
        $businessexpense->update([
            'no_ref' =>  $request->no_ref,
            'date' =>  $request->date,
            'description' =>  $request->desc,
            'value' =>  $request->value,
            'contact' =>  $request->contact,
            'author' =>   $request->user()->name,
            'detail' =>  $request->detail,
        ]);

        //update journal
        $journal->update([
            'no_ref' => $request->no_ref,
            'desc' => $request->desc,
            'value' => $request->value,
            'detail' => $request->detail,
            'date' => $request->date,
            'author' =>   $request->user()->name,
            'source' => "Dari Pengeluaran",
        ]);

        //tambahkan ke arus kas
        //per masing-masing list pada debit
        $this->storeToCashFlow($request, $business);

        //tambahkan ke buku besar
        $this->storeToLedger($request, $business);

        return response()->json([
            'status' => 'success',
            'data' => $businessexpense,
        ]);
    }

    public function destroy(Business $business, Businessexpense $businessexpense)
    {

        $businessexpense->delete();

        //hapus data pada journal dengan dengan mengambil referensi h_ref
        $this->destroyJournal($businessexpense['no_ref'], $business);

        //tambahkan ke arus kas
        //per masing-masing list pada debit
        $this->destroyCashFlow($businessexpense['no_ref'], $business);

        //tambahkan ke buku besar
        $this->destroyLedger($businessexpense['no_ref'], $business);

        return response()->json([
            'status' => 'success',
            'data' => $businessexpense,
        ]);
    }
}
