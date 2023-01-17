<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Models\Businessjournal;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use App\Models\Businesscashmutation;
use Illuminate\Support\Facades\Auth;

class BusinessCashMutationController extends Controller
{
    public function newNoRefCashMutation($no_ref_request, $no_ref_cash_mutation){
        $split_cash_mutation_ref_no = explode("-", $no_ref_cash_mutation);
        $old_ref_no = (int)$split_cash_mutation_ref_no[1];
        $new_ref_no = 1000000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }

    public function noRefCashMutationRecomendation($business)
    {
        $cash_mutation = Businesscashmutation::where('business_id', $business)->orderBy('id', 'desc')->first();

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

    public function storeToJournal($request, $business)
    {
        Businessjournal::create([
            'no_ref' => $request->no_ref,
            'desc' => $request->desc,
            'value' => $request->value,
            'detail' => $request->detail,
            'date' => $request->date,
            'author' =>   $request->user()->name,
            'source' => "Dari Mutasi Kas",
            'business_id' => $business['id']
        ]);
    }

    public function storeToLedger($request, $business)
    {
        //debit: to_account
        $account = Businessaccount::find($request->to_account['id']);
        Businessledger::create([
            'account_name' => $account['name'],
            'account_id' => $account['id'],
            'debit' =>  $request->value,
            'credit' => 0,
            'no_ref' => $request->no_ref,
            'date' => $request->date,
            'description' => $request->desc,
            'account_code' => $account['code'],
            'author' =>$request->user()->name,
            'note' => $request->detail,
            'business_id' => $business['id']
        ]);

        // credit : list 
        //debit: to_account
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
    }

    public function destroyJournal($no_ref, $business)
    {
        Businessjournal::where('business_id', $business['id'])->where('no_ref', $no_ref)->first()->delete();
    }

    public function destroyLedger($no_ref, $business)
    {
        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $no_ref)->get();

        foreach ($ledgers as $ledger) {
            $ledger->delete();
        }
    }

    public function getApiData(Business $business){
        $data = Businesscashmutation::where('business_id', $business['id'])->filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50);
        
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
        return view('business.cash-mutation.index', compact('business'));
    }

    public function create(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.cash-mutation.create', compact('business'));
    }

    public function store(Request $request, Business $business)
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
        $cash_mutation = Businesscashmutation::where('business_id', $business['id'])->where('no_ref', $request->no_ref)->first();
        
        if ($cash_mutation) {
            $request->no_ref = $this->newNoRefCashMutation('CM', $cash_mutation->no_ref);
        }

        //tambah data pada table revenues
        $revenue = Businesscashmutation::create([
            'no_ref' =>  $request->no_ref,
            'date' =>  $request->date,
            'description' =>  $request->desc,
            'value' =>  $request->value,
            'author' =>   $request->user()->name,
            'detail' =>  $request->detail,
            'business_id' => $business['id']
        ]);

        //tambah data pada journal dengan mengisi source : Pendapatan Dari: General Customer
        $this->storeToJournal($request, $business);

        //tambahkan ke buku besar
        $this->storeToLedger($request, $business);

        return response()->json([
            'status' => 'success',
            'data' => $attributes
        ]);
    }

    public function show(Business $business, Businesscashmutation $businesscashmutation)
    {
        $businesscashmutation['created_at_for_human'] = $businesscashmutation->updated_at->diffForHumans();
        $businesscashmutation['is_updated'] = $businesscashmutation->updated_at != $businesscashmutation->created_at ? true : false;
        $businesscashmutation['ledgers'] = Businessledger::where('business_id', $business['id'])->where('no_ref', $businesscashmutation->no_ref)->get();


        return response()->json([
            'status' => 'success',
            'data' => $businesscashmutation,
        ]);
    }

    public function edit(Business $business, Businesscashmutation $businesscashmutation)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.cash-mutation.edit', [
            'cash_mutation_id' => $businesscashmutation['id']
        ]);
    }

    public function update(Request $request, Business $business, Businesscashmutation $businesscashmutation)
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
        $journal = Businessjournal::where('business_id', $business['id'])->where('no_ref', $businesscashmutation['no_ref'])->first();

        //hapus data pada buku besar
        $this->destroyLedger($businesscashmutation['no_ref'], $business);

        //cek apakah no_ref telah digunakan
        $cash_mutation = Businesscashmutation::where('business_id', $business['id'])->where('no_ref', $request->no_ref)->first();

        //update
        $businesscashmutation->update([
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
        $this->storeToLedger($request, $business);

        return response()->json([
            'status' => 'success',
            'data' => $businesscashmutation,
        ]);
    }

    public function destroy(Business $business, Businesscashmutation $businesscashmutation)
    {
        $businesscashmutation->delete();

        //hapus cashMutation pada journal dengan dengan mengambil referensi h_ref
        $this->destroyJournal($businesscashmutation['no_ref'], $business);

        //tambahkan ke buku besar
        $this->destroyLedger($businesscashmutation['no_ref'], $business);

        return response()->json([
            'status' => 'success',
            'data' => $businesscashmutation,
        ]);
    }

    public function printDetail(Business $business, Businesscashmutation $businesscashmutation){
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        $businesscashmutation['date_format'] = Carbon::createFromDate($businesscashmutation->date)->toFormattedDateString();


        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $businesscashmutation->no_ref)->get();

        return view('business.cash-mutation.print-detail', compact('businesscashmutation', 'ledgers', 'business'));
    }

    public function print(Business $business){
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        $businesscashmutations = Businesscashmutation::where('business_id', $business['id'])->filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date', 'asc')->get();
       
        return view('business.cash-mutation.print', [
            'cashMutations' => $businesscashmutations,
            'author' => request()->user(),
            'business' => $business
        ]);
    }
}
