<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Models\Businessjournal;
use App\Models\Businessrevenue;
use App\Models\Businesscashflow;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BusinessRevenueController extends Controller
{
    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.revenue.index', compact('business'));
    }

    public function create(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.revenue.create', compact('business'));
    }

    public function edit(Business $business, Businessrevenue $businessrevenue)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.revenue.edit', [
            'revenue_id' => $businessrevenue['id'],
            'business' => $business
        ]);
    }

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
                'debit' => $list['total'],
                'credit' => 0,
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
            'source' => "Dari Pendapatan",
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
        foreach ($request->listInput as $list) {
            $account = Businessaccount::find($list['accountId']);
            Businessledger::create([
                'account_name' => $account['name'],
                'account_id' => $account['id'],
                'debit' =>  0,
                'credit' => $list['total'],
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

    public function store(Request $request, Business $business)
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
        $revenue = Businessrevenue::create([
            'no_ref' =>  $request->no_ref,
            'date' =>  $request->date,
            'description' =>  $request->desc,
            'value' =>  $request->value,
            'contact' =>  $request->contact,
            'author' =>   $request->user()->name,
            'detail' =>  $request->detail,
            'business_id' =>  $business['id'],
        ]);

        //tambah data pada journal dengan mengisi source : Pendapatan Dari: General Customer
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

    public function update(Request $request,Business $business, Businessrevenue $businessrevenue)
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

        //dapatkan data journal berdasarkan no_href
        $journal = Businessjournal::where('business_id', $business['id'])->where('no_ref', $businessrevenue['no_ref'])->first();

        //hapus data pada arus kas
        $this->destroyCashFlow($businessrevenue['no_ref'], $business);

        //hapus data pada buku besar
        $this->destroyLedger($businessrevenue['no_ref'], $business);

        //update
        // update revenue
        $businessrevenue->update([
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
        $this->storeToCashFlow($request, $business);

        //tambahkan ke buku besar
        $this->storeToLedger($request, $business);

        return response()->json([
            'status' => 'success',
            'data' => $businessrevenue,
        ]);
    }

    public function show(Business $business, Businessrevenue $businessrevenue)
    {
        $businessrevenue['created_at_for_human'] = $businessrevenue->updated_at->diffForHumans();
        $businessrevenue['is_updated'] = $businessrevenue->updated_at != $businessrevenue->created_at ? true : false;
        $businessrevenue['ledgers'] = Businessledger::where('no_ref', $businessrevenue->no_ref)->get();


        return response()->json([
            'status' => 'success',
            'data' => $businessrevenue,
        ]);
    }

    public function destroy(Business $business, Businessrevenue $businessrevenue)
    {
        $businessrevenue->delete();

        //hapus businessrevenue pada journal dengan dengan mengambil referensi h_ref
        $this->destroyJournal($businessrevenue['no_ref'], $business);

        //tambahkan ke arus kas
        //per masing-masing list pada debit
        $this->destroyCashFlow($businessrevenue['no_ref'], $business);

        //tambahkan ke buku besar
        $this->destroyLedger($businessrevenue['no_ref'], $business);

        return response()->json([
            'status' => 'success',
            'data' => $businessrevenue,
        ]);
    }

    public function getApiData(Business $business){
        $data = Businessrevenue::where('business_id', $business['id'])->filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50);
        
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

    public function noRefRevenueRecomendation(Business $business)
    {
        $revenue = Businessrevenue::where('business_id', $business['id'])->orderBy('id', 'desc')->first();

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

    public function printDetail(Business $business, Businessrevenue $businessrevenue){
        $businessrevenue['date_format'] = Carbon::createFromDate($businessrevenue->date)->toFormattedDateString();

        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $businessrevenue->no_ref)->get();

        return view('business.revenue.print-detail', compact('businessrevenue', 'ledgers', 'business'));
    }

    public function print(Business $business){
        $revenues = Businessrevenue::where('business_id', $business['id'])->filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date', 'asc')->get();
       
        return view('business.revenue.print', [
            'revenues' => $revenues,
            'author' => request()->user(),
            'business' => $business
        ]);
    }
}
