<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ledger;
use App\Models\Account;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.ledger.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function print(){
        $account = Account::find(request('account_id'));
        // dd($account);
        $ledgers = Ledger::filter(request(['account_id','search','date_from','date_to','this_week','this_month','this_year']))
                        ->orderBy('date', 'asc')->get();

        //get all
        $amountLedgerAll = Ledger::filter(request(['outlet_id','account_id','date_to', 'end_week', 'end_month', 'end_year']))
                                ->get();

        //get base on filtering by date or time
        $amountLedger = Ledger::filter(request(['outlet_id','account_id','search','date_from','date_to','this_week','this_month','this_year']))
                                ->eachAccount(request(['lost_profit']))
                                ->get();

        $period = '';

        
        if (request('date_from') && request('date_to')) {
            $period =  request('date_from') == request('date_to') ? Carbon::parse(request('date_from'))->isoformat('MMM, D Y') : Carbon::parse(request('date_from'))->isoformat('MMM, D Y') . ' - ' . Carbon::parse(request('date_to'))->isoformat('MMM, D Y');
        } elseif (request('this_week')) {
            $period = Carbon::parse(now()->startOfWeek())->isoformat('MMM, D Y') . ' - ' . Carbon::parse(now()->endOfWeek())->isoformat('MMM, D Y');
            
        } elseif (request('this_month'))
        {
            $period = Carbon::now()->isoformat('MMMM, Y');
        } else{
            $period = Carbon::now()->isoformat('Y');
        }

        return view('admin.ledger.print', [
            'account' => $account,
            'ledgers' => $ledgers,
            'total_debit' => $amountLedger->sum('debit'),
            'total_credit' => $amountLedger->sum('credit'),
            'amountLedger' => $amountLedgerAll->sum('debit') - $amountLedgerAll->sum('credit'),
            'author' => request()->user(),
            'period' => $period,

        ]);
    }

    public function getApiData()
    {
        $ledgers = Ledger::filter(request(['account_id','search','date_from','date_to','this_week','this_month','this_year']))
                        ->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50);

        //get all
        $amountLedgerAll = Ledger::filter(request(['account_id','date_to', 'end_week','end_month','end_year']))
                                ->get();

        //get base on filtering by date or time
        $amountLedger = Ledger::filter(request(['account_id','search','date_from','date_to','this_week','this_month','this_year']))
                                ->eachAccount(request(['lost_profit']))
                                ->get();


        return response()->json([
            'status' => 'success',
            'data' => [
                'ledgers' => $ledgers,
                'total_debit' => $amountLedger->sum('debit'),
                'total_credit' => $amountLedger->sum('credit'),
                'amountLedger' => $amountLedgerAll->sum('debit') - $amountLedgerAll->sum('credit')
            ]
        ]);
    }
}
