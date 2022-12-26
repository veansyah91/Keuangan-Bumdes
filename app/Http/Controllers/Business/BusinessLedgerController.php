<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Http\Controllers\Controller;

class BusinessLedgerController extends Controller
{
    public function print(Business $business){
        $account = Businessaccount::find(request('account_id'));
        // dd($account);
        $ledgers = Businessledger::filter(request(['account_id','search','date_from','date_to','this_week','this_month','this_year']))
                        ->orderBy('date', 'asc')->get();

        //get all
        $amountLedgerAll = Businessledger::filter(request(['outlet_id','account_id','date_to', 'end_week', 'end_month', 'end_year']))
                                ->get();

        //get base on filtering by date or time
        $amountLedger = Businessledger::filter(request(['outlet_id','account_id','search','date_from','date_to','this_week','this_month','this_year']))
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

        return view('business.ledger.print', [
            'account' => $account,
            'business' => $business,
            'ledgers' => $ledgers,
            'total_debit' => $amountLedger->sum('debit'),
            'total_credit' => $amountLedger->sum('credit'),
            'amountLedger' => $amountLedgerAll->sum('debit') - $amountLedgerAll->sum('credit'),
            'author' => request()->user(),
            'period' => $period,

        ]);
    }

    public function getApiData(Business $business)
    {
        $ledgers = Businessledger::filter(request(['account_id','search','date_from','date_to','this_week','this_month','this_year']))
                                ->where('business_id', $business['id'])
                        ->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50);

        //get all
        $amountLedgerAll = Businessledger::filter(request(['account_id','date_to', 'end_week','end_month','end_year']))
                                ->where('business_id', $business['id'])
                                ->get();

        //get base on filtering by date or time
        $amountLedger = Businessledger::filter(request(['account_id','search','date_from','date_to','this_week','this_month','this_year']))
                                ->where('business_id', $business['id'])
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
    public function index(Business $business)
    {
        return view('business.ledger.index', compact('business'));
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
     * @param  \App\Models\Businessledger  $businessledger
     * @return \Illuminate\Http\Response
     */
    public function show(Businessledger $businessledger)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Businessledger  $businessledger
     * @return \Illuminate\Http\Response
     */
    public function edit(Businessledger $businessledger)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Businessledger  $businessledger
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Businessledger $businessledger)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Businessledger  $businessledger
     * @return \Illuminate\Http\Response
     */
    public function destroy(Businessledger $businessledger)
    {
        //
    }
}
