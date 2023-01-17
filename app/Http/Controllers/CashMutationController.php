<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ledger;
use App\Models\Account;
use App\Models\Journal;
use App\Models\CashMutation;
use Illuminate\Http\Request;

class CashMutationController extends Controller
{
    public function newNoRefCashMutation($no_ref_request, $no_ref_cash_mutation){
        $split_cash_mutation_ref_no = explode("-", $no_ref_cash_mutation);
        $old_ref_no = (int)$split_cash_mutation_ref_no[1];
        $new_ref_no = 1000000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }

    public function noRefCashMutationRecomendation()
    {
        $cash_mutation = CashMutation::orderBy('id', 'desc')->first();

        $fix_ref_no = '';

        if($cash_mutation){
            $fix_ref_no = $this->newNoRefCashMutation('CM', $cash_mutation->no_ref);
        }else{
            $fix_ref_no = 'CM-000001';
        }

        return response()->json([
            'status' => 'success',
            'data' => $fix_ref_no,
        ]);
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
            'source' => "Dari Mutasi Kas",
        ]);
    }

    public function storeToLedger($request)
    {
        //debit: to_account
        $account = Account::find($request->to_account['id']);
        Ledger::create([
            'account_name' => $account['name'],
            'account_id' => $account['id'],
            'debit' =>  $request->value,
            'credit' => 0,
            'no_ref' => $request->no_ref,
            'date' => $request->date,
            'description' => $request->desc,
            'account_code' => $account['code'],
            'author' =>$request->user()->name,
            'note' => $request->detail
        ]);

        // credit : list 
        //debit: to_account
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
    }

    public function destroyJournal($no_ref)
    {
        Journal::where('no_ref', $no_ref)->first()->delete();
    }

    public function destroyLedger($no_ref)
    {
        $ledgers = Ledger::where('no_ref', $no_ref)->get();

        foreach ($ledgers as $ledger) {
            $ledger->delete();
        }
    }

    public function getApiData(){
        $data = CashMutation::filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50);
        
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
    
    public function index()
    {
        return view('admin.cash-mutation.index');
    }

    public function create()
    {
        return view('admin.cash-mutation.create');
    }

    public function store(Request $request)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',
            'date' => 'date',
            'desc' => 'required',
            'value' => 'required|numeric',
            'detail' => 'string|nullable',
        ]);

        //cek apakah no_ref telah digunakan
        $cash_mutation = CashMutation::where('no_ref', $request->no_ref)->first();
        
        if ($cash_mutation) {
            $request->no_ref = $this->newNoRefCashMutation('CM', $cash_mutation->no_ref);
        }

        //tambah data pada table revenues
        $revenue = CashMutation::create([
            'no_ref' =>  $request->no_ref,
            'date' =>  $request->date,
            'description' =>  $request->desc,
            'value' =>  $request->value,
            'author' =>   $request->user()->name,
            'detail' =>  $request->detail,
        ]);

        //tambah data pada journal dengan mengisi source : Pendapatan Dari: General Customer
        $this->storeToJournal($request);

        //tambahkan ke buku besar
        $this->storeToLedger($request);

        return response()->json([
            'status' => 'success',
            'data' => $attributes
        ]);
    }

    public function show(CashMutation $cashMutation)
    {
        $cashMutation['created_at_for_human'] = $cashMutation->updated_at->diffForHumans();
        $cashMutation['is_updated'] = $cashMutation->updated_at != $cashMutation->created_at ? true : false;
        $cashMutation['ledgers'] = Ledger::where('no_ref', $cashMutation->no_ref)->get();


        return response()->json([
            'status' => 'success',
            'data' => $cashMutation,
        ]);
    }

    public function edit(CashMutation $cashMutation)
    {
        return view('admin.cash-mutation.edit', [
            'cash_mutation_id' => $cashMutation['id']
        ]);
    }

    public function update(Request $request, CashMutation $cashMutation)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',
            'date' => 'date',
            'desc' => 'required',
            'value' => 'required|numeric',
            'detail' => 'string|nullable',
        ]);

        //dapatkan data journal berdasarkan no_href
        $journal = Journal::where('no_ref', $cashMutation['no_ref'])->first();

        //hapus data pada buku besar
        $this->destroyLedger($cashMutation['no_ref']);

        //cek apakah no_ref telah digunakan
        $cash_mutation = CashMutation::where('no_ref', $request->no_ref)->first();

        //update
        $cashMutation->update([
            'no_ref' =>  $request->no_ref,
            'date' =>  $request->date,
            'description' =>  $request->desc,
            'value' =>  $request->value,
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
            'source' => "Dari Mutasi Kas",
        ]);

        //tambahkan ke buku besar
        $this->storeToLedger($request);

        return response()->json([
            'status' => 'success',
            'data' => $cashMutation,
        ]);
    }

    public function destroy(CashMutation $cashMutation)
    {
        $cashMutation->delete();

        //hapus cashMutation pada journal dengan dengan mengambil referensi h_ref
        $this->destroyJournal($cashMutation['no_ref']);

        //tambahkan ke buku besar
        $this->destroyLedger($cashMutation['no_ref']);

        return response()->json([
            'status' => 'success',
            'data' => $cashMutation,
        ]);
    }

    public function printDetail($id){
        $cashMutation = CashMutation::find($id);
        $cashMutation['date_format'] = Carbon::createFromDate($cashMutation->date)->toFormattedDateString();


        $ledgers = Ledger::where('no_ref', $cashMutation->no_ref)->get();

        return view('admin.cash-mutation.print-detail', compact('cashMutation', 'ledgers'));
    }

    public function print(){
        $cashMutations = CashMutation::filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date', 'asc')->get();
       
        return view('admin.cash-mutation.print', [
            'cashMutations' => $cashMutations,
            'author' => request()->user()
        ]);
    }
}
