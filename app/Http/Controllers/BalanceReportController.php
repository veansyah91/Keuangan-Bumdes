<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\Ledger;
use App\Models\Account;
use App\Models\Identity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SubClassificationAccount;

class BalanceReportController extends Controller
{
    public function index(){
        return view('admin.report.balance.index');
    }

    public function print(){
        return view('admin.report.balance.print', [
            'author' => request()->user(),
        ]);
    }

    public function year(){
        return view('admin.report.balance.year');
    }

    public function printYear(){
        $identity = Identity::first();
        return view('admin.report.balance.print-year', [
            'author' => request()->user(),
            'identity' => $identity
        ]);
    }

    public function getApiData(){
        
        $data = Account::where('code','<','4100000')
                        ->whereHas('ledgers')
                        ->orderBy('code', 'asc')
                        ->get();

        $lost_profit_ledger = Ledger::filter(request(['date_to', 'end_week', 'end_month', 'end_year']))
                                    ->whereHas('account', function($query){
                                        $query->where('code', 'like', '4%')
                                                ->orWhere('code', 'like', '5%');
                                    })
                                    ->get();

        $lost_profit = $lost_profit_ledger->sum('credit') - $lost_profit_ledger->sum('debit');

        $is_there_current_year_earnings = false;

        foreach ($data as  $d) {

            $ledgers = Ledger::filter(request(['date_to', 'end_week', 'end_month', 'end_year']))->where('account_id', $d['id'])->get();

            if ($d['name'] == 'Laba Tahun Berjalan') {
                $d['total'] = $ledgers->sum('debit') - $ledgers->sum('credit') - $lost_profit;
                $is_there_current_year_earnings = true;
            } else {
                $d['total'] = $ledgers->sum('debit') - $ledgers->sum('credit');
            }
        }

        if (!$is_there_current_year_earnings) {
            $data[count($data)] = [
                "name" => "Laba Tahun Berjalan",
                "code" => "3700001",
                "total" => -1 * $lost_profit
            ];
        }

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

        return response()->json([
            'status' => 'success',
            'data' => [
                'balance' => $data,
                'period' => $period,
                'count' => count($data),
            ],
        ]); 
    }

    public function getApiDataYear(){
        $balanceNow = SubClassificationAccount::where('code','<','4100000')
                                            ->has('accounts')
                                            ->with('accounts', function($query){
                                                $query->where('code','<','4100000')
                                                        ->whereHas('ledgers')       
                                                        ->with('ledgers', function($query){
                                                                $query->whereYear('date','<=', request('year'));
                                                        });
                                            })
                                            ->get();                                            
                                                     
                                        
        $reportYear = [];
        $i = 0;

        foreach ($balanceNow as $sub) {
            if (count($sub->accounts) > 0) {
                foreach ($sub->accounts as $account) {
                    if (count($account->ledgers) > 0) {
                        $reportYear[$i] = $sub;
                    }
                }
                $i++;
            }
        }
        
        $lost_profit_ledger_now = Ledger::whereYear('date','<=', request('year'))
                                    ->whereHas('account', function($query){
                                        $query->where('code', 'like', '4%')
                                                ->orWhere('code', 'like', '5%');
                                    })
                                    ->get();
        $lost_profit_now = $lost_profit_ledger_now->sum('credit') - $lost_profit_ledger_now->sum('debit');

        
        $lost_profit_ledger_before = Ledger::whereYear('date','<', request('year'))
                                            ->whereHas('account', function($query){
                                                $query->where('code', 'like', '4%')
                                                        ->orWhere('code', 'like', '5%');
                                            })
                                            ->get();
                                            
        $lost_profit_before = $lost_profit_ledger_before->sum('credit') - $lost_profit_ledger_before->sum('debit');

        $j = 0;
        $is_there_current_year_earnings_now = false;
        $is_there_current_year_earnings_before = false;

        if (count($reportYear) > 0) {
            foreach ($reportYear as $report) {
                $totalNow = 0;
                $totalBefore = 0;
                foreach ($report->accounts as $account) {
                    foreach ($account->ledgers as $ledger) {
                        $time = DateTime::createFromFormat("Y-m-d", $ledger->date);
                        if ($time->format("Y") < request('year')) {
                            $report['name'] == 'Laba Tahun Berjalan' ? 
                                        $is_there_current_year_earnings_before = true :
                                        $is_there_current_year_earnings_before = false;
                            $totalBefore += $ledger->debit - $ledger->credit;
                        }
                        $totalNow += $ledger->debit - $ledger->credit;

                        $report['name'] == 'Laba Tahun Berjalan' ? 
                                        $is_there_current_year_earnings_now = true :
                                        $is_there_current_year_earnings_now = false;                        
                    }
                }

                if ($report['name'] == 'Laba Tahun Berjalan') {
                    $report['total_now'] = $totalNow + $lost_profit_now;
                    $report['total_before'] = $totalBefore + $lost_profit_before;
                } else {
                    $report['total_now'] = $totalNow;
                    $report['total_before'] = $totalBefore;
                }

                $j++;
            }
        }

        if (!$is_there_current_year_earnings_now || !$is_there_current_year_earnings_before) {
            array_push($reportYear, [
                    "name" => "Laba Tahun Berjalan",
                    "code" => "3700001",
                    "total_now" => -1 * ($lost_profit_now),
                    "total_before" => -1 * ($lost_profit_before),
            ]);
        }

        $period = Carbon::now()->isoformat(request('year'));
    
        return response()->json([
            'status' => 'success',
            'data' => [
                'balance' => $reportYear,
                'period' => $period,
                'balances' => $balanceNow,
            ],
        ]); 
    }
}
