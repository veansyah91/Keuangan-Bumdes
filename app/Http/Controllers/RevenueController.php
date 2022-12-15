<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ledger;
use App\Models\Account;
use App\Models\Journal;
use App\Models\Revenue;
use App\Models\Cashflow;
use Illuminate\Http\Request;
use App\Http\Resources\RevenueCollection;

class RevenueController extends Controller
{
    public function index()
    {
        return view('admin.revenue.index');
    }

    public function create()
    {
        return view('admin.revenue.create');
    }

    public function edit($id)
    {
        return view('admin.revenue.edit', [
            'revenue_id' => $id
        ]);
    }

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
                'debit' => $list['total'],
                'credit' => 0,
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
            'source' => "Dari Pendapatan",
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
        foreach ($request->listInput as $list) {
            $account = Account::find($list['accountId']);
            Ledger::create([
                'account_name' => $account['name'],
                'account_id' => $account['id'],
                'debit' =>  0,
                'credit' => $list['total'],
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

    public function store(Request $request)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',
            'contact' => 'required',
            'desc' => 'required',
            'value' => 'required|numeric',
            'date' => 'date',
            'detail' => 'string|nullable',
        ]);

        //tambah data pada table revenues
        $revenue = Revenue::create([
            'no_ref' =>  $request->no_ref,
            'date' =>  $request->date,
            'description' =>  $request->desc,
            'value' =>  $request->value,
            'contact' =>  $request->contact,
            'author' =>   $request->user()->name,
            'detail' =>  $request->detail,
        ]);

        //tambah data pada journal dengan mengisi source : Pendapatan Dari: General Customer
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

    public function update(Request $request, $id)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',
            'contact' => 'required',
            'desc' => 'required',
            'value' => 'required|numeric',
            'date' => 'date',
            'detail' => 'string|nullable',
        ]);

        // ambil acuan revenue lama utk hapus data di ledger, cashflow, journal
        $revenue = Revenue::find($id);

        //dapatkan data journal berdasarkan no_href
        $journal = Journal::where('no_ref', $revenue['no_ref'])->first();

        //hapus data pada arus kas
        $this->destroyCashFlow($revenue['no_ref']);

        //hapus data pada buku besar
        $this->destroyLedger($revenue['no_ref']);

        //update
        // update revenue
        $revenue->update([
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
            'source' => "Dari Pendapatan",
        ]);

        //tambahkan ke arus kas
        //per masing-masing list pada debit
        $this->storeToCashFlow($request);

        //tambahkan ke buku besar
        $this->storeToLedger($request);

        return response()->json([
            'status' => 'success',
            'data' => $revenue,
        ]);
    }

    public function show($id)
    {
        $data = Revenue::find($id);
        $data['created_at_for_human'] = $data->updated_at->diffForHumans();
        $data['is_updated'] = $data->updated_at != $data->created_at ? true : false;
        $data['ledgers'] = Ledger::where('no_ref', $data->no_ref)->get();


        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function destroy($id)
    {
        $data = Revenue::find($id);

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

    public function getApiData(){
        $data = Revenue::filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50);
        
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function newNoRefRevenue($no_ref_request, $no_ref_revenue){
        $split_revenue_ref_no = explode("-", $no_ref_revenue);
        $old_ref_no = (int)$split_revenue_ref_no[1];
        $new_ref_no = 1000000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }

    public function noRefRevenueRecomendation()
    {
        // $ref_no = explode("-", request('search'));
        $revenue = Revenue::orderBy('id', 'desc')->first();

        $fix_ref_no = '';

        if($revenue){
            $fix_ref_no = $this->newNoRefrevenue('CI', $revenue->no_ref);
        }else{
            $fix_ref_no = 'CI-000001';
        }

        return response()->json([
            'status' => 'success',
            'data' => $fix_ref_no,
        ]);
    }

    public function printDetail($id){
        $revenue = Revenue::find($id);
        $revenue['date_format'] = Carbon::createFromDate($revenue->date)->toFormattedDateString();


        $ledgers = Ledger::where('no_ref', $revenue->no_ref)->get();

        return view('admin.revenue.print-detail', compact('revenue', 'ledgers'));
    }

    public function print(){
        $revenues = new RevenueCollection(Revenue::filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date', 'asc')->get());
       
        return view('admin.revenue.print', [
            'revenues' => $revenues,
            'author' => request()->user()
        ]);
    }
}
