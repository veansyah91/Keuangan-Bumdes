<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Business;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\Businessaccount;
use App\Models\Businesscashflow;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\SubClassificationAccount;

class BusinessCashflowReportController extends Controller
{
    public function getApiData(Business $business){
        $i = 0;
        $businessCashflows = [];

        $accounts = Businessaccount::where('business_id', $business['id'])->whereIsActive(true)->get();

        foreach ($accounts as $ly) {
            $businessCashflowTemp = Businesscashflow::where('business_id', $business['id'])->filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->where('account_id', $ly->id)->get();

            if ($businessCashflowTemp->sum('debit') > 0 || $businessCashflowTemp->sum('credit') > 0) {
                $businessCashflows[$i] = [
                    'account_name' => $businessCashflowTemp[0]['account_name'],
                    'type' => $businessCashflowTemp[0]['type'],
                    'debit' => $businessCashflowTemp->sum('debit'),
                    'credit' => $businessCashflowTemp->sum('credit'),
                ];
                $i++;
            }
        }        

        $lastYears = [];

        $subaccounts = SubClassificationAccount::all();

        $i = 0;
        foreach ($subaccounts as $subaccount) {
            $accounts = Businessaccount::where('business_id', $business['id'])->where('sub_classification_account_id', $subaccount->id)->whereIsActive(true)
                                        ->get();

            
            foreach ($accounts as $ly) {
                $businessCashflowTemps = Businesscashflow::where('business_id', $business['id'])->where('account_id', $ly->id)
                                                        ->where(function($query){
                                                            $query->whereYear('date', request('year'))
                                                            ->orWhereYear('date', request('year')-1);
                                                        })
                                                        ->get();

                if (count($businessCashflowTemps) > 0) {
                    $lastYears[$i]['cashflows'] = $businessCashflowTemps;
                    $lastYears[$i]['sub'] = $subaccount->name;
                    $lastYears[$i]['sub_code'] = $subaccount->code;
                    $i++;
                }
            }
        }

        $totalBalance = Businesscashflow::where('business_id', $business['id'])->where('business_id', $business['id'])
                                        ->filter(request(['date_to', 'end_week','end_month','end_year']))
                                        ->get();
        
        $period = '';
        if (request('date_from') && request('date_to')) {
            $period = request('date_from') == request('date_to') ? Carbon::parse(request('date_from'))->isoformat('MMM, D Y') : Carbon::parse(request('date_from'))->isoformat('MMM, D Y') . ' - ' . Carbon::parse(request('date_to'))->isoformat('MMM, D Y');
        } elseif (request('this_week')) {
            $period = Carbon::parse(now()->startOfWeek())->isoformat('MMM, D Y') . ' - ' . Carbon::parse(now()->endOfWeek())->isoformat('MMM, D Y');
            
        } elseif (request('this_month'))
        {
            $period = Carbon::now()->isoformat('MMMM, Y');
        } else{
            $period = Carbon::now()->isoformat('Y');
        }

        if (request('year')) {
            $period = Carbon::now()->isoformat(request('year'));
            $totalBalance = Businesscashflow::where('business_id', $business['id'])->whereYear('date', '<=', request('year'))
                                ->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'cashFlows' => $businessCashflows,
                'period' => $period,
                'business' => $business,
                'totalBalance' => $totalBalance->sum('debit') - $totalBalance->sum('credit'),
                'lastYear' => $lastYears,
            ],
        ]); 
    }

    public function print(Business $business){
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.report.cashflow.print', [
            'author' => request()->user(),
            'business' => $business
        ]);
    }

    public function printYear(Business $business){
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        $identity = Identity::first();
        return view('business.report.cashflow.print-year', [
            'author' => request()->user(),
            'identity' => $identity,
            'business' => $business
        ]);
    }

    public function year(Business $business){
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.report.cashflow.year', compact('business'));
    }

    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.report.cashflow.index', compact('business'));
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
     * @param  \App\Models\Businesscashflow  $businesscashflow
     * @return \Illuminate\Http\Response
     */
    public function show(Businesscashflow $businesscashflow)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Businesscashflow  $businesscashflow
     * @return \Illuminate\Http\Response
     */
    public function edit(Businesscashflow $businesscashflow)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Businesscashflow  $businesscashflow
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Businesscashflow $businesscashflow)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Businesscashflow  $businesscashflow
     * @return \Illuminate\Http\Response
     */
    public function destroy(Businesscashflow $businesscashflow)
    {
        //
    }
}
