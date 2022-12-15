<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Account;
use App\Models\Cashflow;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\SubClassificationAccount;

class CashflowReportController extends Controller
{
    public function index(){
        return view('admin.report.cashflow.index');
    }

    public function year(){
        return view('admin.report.cashflow.year');
    }

    public function print(){

        return view('admin.report.cashflow.print', [
            'author' => request()->user(),
        ]);
    }

    public function printYear(){
        $identity = Identity::first();
        return view('admin.report.cashflow.print-year', [
            'author' => request()->user(),
            'identity' => $identity
        ]);
    }

    public function getApiData(){

        $subs = SubClassificationAccount::has('accounts')
                                        ->with('accounts', function($query){
                                            $query->whereHas('cashflows')       
                                                  ->with('cashflows', function($query){
                                                        $query->whereYear('date', request('year'))
                                                              ->orWhereYear('date', request('year')-1);
                                                  });
                                        })
                                        ->get();
                                        
        $lastYear = [];

        $i = 0;
        foreach ($subs as $sub) {
            if (count($sub->accounts) > 0) {
                foreach ($sub->accounts as $account) {
                    if (count($account->cashflows) > 0) {
                        $lastYear[$i] = $sub;
                    }
                }
                $i++;
            }
        }

        $data = Account::whereHas('cashflows')
                        ->with('cashflows', function($query){
                            $query->filter(request(['search','date_from','date_to','this_week','this_month','this_year']));
                        })
                        ->get();

        
        $totalBalance = Cashflow::filter(request(['date_to', 'end_week','end_month','end_year']))
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
            $totalBalance = Cashflow::whereYear('date', '<=', request('year'))
                                ->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'cashFlows' => $data,
                'period' => $period,
                'totalBalance' => $totalBalance->sum('debit') - $totalBalance->sum('credit'),
                'lastYear' => $lastYear,
                'subs' => $subs,
            ],
        ]); 
    }
}
