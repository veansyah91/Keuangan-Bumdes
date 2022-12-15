<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ledger;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Journal;
use App\Models\Cashflow;
use Illuminate\Http\Request;
use App\Http\Resources\ExpenseCollection;

class ExpenseController extends Controller
{
    public function storeToCashFlow($request)
    {
        foreach ($request->listInput as $list) {
            $account = Account::find($list['accountId']);

            $type = 'operation';

            $temp =  substr($account['code'], 0 , 2);
                
            if ((int)$temp > 15 && (int)$temp < 20) {
                $type = 'investment';
            }

            if ((int)$temp > 29 && (int)$temp < 40) {
                $type = 'finance';
            }

            Cashflow::create([
                'account_id' => $account['id'],
                'no_ref' => $request->no_ref,
                'date' => $request->date,
                'account_code' => $account['code'],
                'account_name' => $account['name'],
                'type' => $type,
                'debit' => 0,
                'credit' => $list['total'],
            ]);
        }
    }

    public function storeToJournal($request)
    {
        Journal::create([
            'no_ref' => $request->no_ref,
            'desc' => $request->desc,
            'value' => $request->value,
            'detail' => $request->detail,
            'date' => $request->date,
            'author' =>   $request->user()->name,
            'source' => "Dari Pengeluaran",
        ]);
    }

    public function storeToLedger($request)
    {
        //debit: from_account
        $account = Account::find($request->from_account['id']);
        Ledger::create([
            'account_name' => $account['name'],
            'account_id' => $account['id'],
            'debit' =>  0,
            'credit' => $request->value,
            'no_ref' => $request->no_ref,
            'date' => $request->date,
            'description' => $request->desc,
            'account_code' => $account['code'],
            'author' =>$request->user()->name,
            'note' => $request->detail
        ]);

        // credit : list 
        foreach ($request->listInput as $list) {
            $account = Account::find($list['accountId']);
            Ledger::create([
                'account_name' => $account['name'],
                'account_id' => $account['id'],
                'debit' =>  $list['total'],
                'credit' => 0,
                'no_ref' => $request->no_ref,
                'date' => $request->date,
                'description' => $request->desc,
                'account_code' => $account['code'],
                'author' =>$request->user()->name,
                'note' => $request->detail
            ]);
        }
    }

    public function destroyJournal($no_ref)
    {
        Journal::where('no_ref', $no_ref)->first()->delete();
    }

    public function destroyCashflow($no_ref)
    {
        $cashflows = Cashflow::where('no_ref', $no_ref)->get();

        foreach ($cashflows as $cashflow) {
            $cashflow->delete();
        }
    }

    public function destroyLedger($no_ref)
    {
        $ledgers = Ledger::where('no_ref', $no_ref)->get();

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

    public function noRefExpenseRecomendation()
    {
        // $ref_no = explode("-", request('search'));
        $expense = Expense::orderBy('id', 'desc')->first();

        $fix_ref_no = '';

        if($expense){
            $fix_ref_no = $this->newNoRefExpense('CO', $expense->no_ref);
        }else{
            $fix_ref_no = 'CO-000001';
        }

        return response()->json([
            'status' => 'success',
            'data' => $fix_ref_no,
        ]);
    }

    public function getApiData(){
        $data = Expense::filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50);
        
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function index()
    {
        return view('admin.expense.index');
    }

    public function create()
    {
        return view('admin.expense.create');
    }

    public function store(Request $request)
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
        $expense = Expense::create([
            'no_ref' =>  $request->no_ref,
            'date' =>  $request->date,
            'description' =>  $request->desc,
            'value' =>  $request->value,
            'contact' =>  $request->contact,
            'author' =>   $request->user()->name,
            'detail' =>  $request->detail,
        ]);

        //tambah data pada journal dengan mengisi source : Pengeluaran Dari: General Customer
        $this->storeToJournal($request);

        //tambahkan ke arus kas
        //per masing-masing list pada debit
        $this->storeToCashFlow($request);

        //tambahkan ke buku besar
        $this->storeToLedger($request);

        return response()->json([
            'status' => 'success',
            'data' => $attributes
        ]);
    }

    public function printDetail($id){
        $expense = Expense::find($id);
        $expense['date_format'] = Carbon::createFromDate($expense->date)->toFormattedDateString();


        $ledgers = Ledger::where('no_ref', $expense->no_ref)->get();

        return view('admin.expense.print-detail', compact('expense', 'ledgers'));
    }

    public function print(){
        $expenses = new ExpenseCollection(Expense::filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date', 'asc')->get());
       
        return view('admin.expense.print', [
            'expenses' => $expenses,
            'author' => request()->user()
        ]);
    }

    public function show($id)
    {
        $data = Expense::find($id);
        $data['created_at_for_human'] = $data->updated_at->diffForHumans();
        $data['is_updated'] = $data->updated_at != $data->created_at ? true : false;
        $data['ledgers'] = Ledger::where('no_ref', $data->no_ref)->get();


        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function edit($id)
    {
        return view('admin.expense.edit', [
            'expense_id' => $id
        ]);
    }

    public function update(Request $request, $id)
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

        // ambil acuan expense lama utk hapus data di ledger, cashflow, journal
        $expense = Expense::find($id);

        //dapatkan data journal berdasarkan no_href
        $journal = Journal::where('no_ref', $expense['no_ref'])->first();

        //hapus data pada arus kas
        $this->destroyCashFlow($expense['no_ref']);

        //hapus data pada buku besar
        $this->destroyLedger($expense['no_ref']);

        //update
        // update expense
        $expense->update([
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
        $this->storeToCashFlow($request);

        //tambahkan ke buku besar
        $this->storeToLedger($request);

        return response()->json([
            'status' => 'success',
            'data' => $expense,
        ]);
    }

    public function destroy($id)
    {
        $data = Expense::find($id);

        $data->delete();

        //hapus data pada journal dengan dengan mengambil referensi h_ref
        $this->destroyJournal($data['no_ref']);

        //tambahkan ke arus kas
        //per masing-masing list pada debit
        $this->destroyCashFlow($data['no_ref']);

        //tambahkan ke buku besar
        $this->destroyLedger($data['no_ref']);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
