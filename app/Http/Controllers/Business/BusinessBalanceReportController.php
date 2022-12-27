<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use DateTime;
use App\Models\Business;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Http\Controllers\Controller;
use App\Models\SubClassificationAccount;

class BusinessBalanceReportController extends Controller
{
    public function index(Business $business){
        return view('business.report.balance.index', compact('business'));
    }

    public function print(Business $business){
        return view('business.report.balance.print', [
            'author' => request()->user(),
            'business' => $business,
        ]);
    }

    public function year(Business $business){
        return view('business.report.balance.year', compact('business'));
    }

    public function printYear(Business $business){
        $identity = Identity::first();
        return view('business.report.balance.print-year', [
            'author' => request()->user(),
            'identity' => $identity,
            'business' => $business,
        ]);
    }

    public function getApiData(Business $business){
        
        $data = Businessaccount::where('business_id', $business['id'])->where('code','<','4100000')
                        ->whereHas('ledgers')
                        ->orderBy('code', 'asc')
                        ->get();

        $lost_profit_ledger = Businessledger::where('business_id', $business['id'])->filter(request(['date_to', 'end_week', 'end_month', 'end_year']))
                                    ->whereHas('account', function($query){
                                        $query->where('code', 'like', '4%')
                                                ->orWhere('code', 'like', '5%');
                                    })
                                    ->get();

        $lost_profit = $lost_profit_ledger->sum('credit') - $lost_profit_ledger->sum('debit');

        $is_there_current_year_earnings = false;

        foreach ($data as  $d) {

            $ledgers = Businessledger::where('business_id', $business['id'])->filter(request(['date_to', 'end_week', 'end_month', 'end_year']))->where('account_id', $d['id'])->get();

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
                "code" => "3299999",
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

    public function getApiDataYear(Business $business){
        $lastYears = [];
        $balances = [];

        $subaccounts = SubClassificationAccount::where('code','<','4100000')->get();
                                            $dates = [];
        $i = 0;
        foreach ($subaccounts as $subaccount) {
            $accounts = Businessaccount::where('business_id', $business['id'])->where('sub_classification_account_id', $subaccount->id)        
                                        ->whereIsActive(true)
                                        ->get();

            foreach ($accounts as $ly) {
                $businessLedgerTemps = Businessledger::where('business_id', $business['id'])->where('account_id', $ly->id)
                                                        ->where(function($query){
                                                            $query->whereYear('date', request('year'))
                                                            ->orWhereYear('date', request('year')-1);
                                                        })
                                                        ->orderBy('date')
                                                        ->get();

                if (count($businessLedgerTemps) > 0) {
                    $totalNow = 0;
                    $totalBefore = 0;
                    foreach ($businessLedgerTemps as $businessLedgerTemp) {
                        $time = DateTime::createFromFormat("Y-m-d", $businessLedgerTemp->date);
                        if ($time->format("Y") < request('year')) {
                            $totalBefore += $businessLedgerTemp->debit - $businessLedgerTemp->credit;
                        }
                        else {
                            $totalNow += $businessLedgerTemp->debit - $businessLedgerTemp->credit;
                        }
                    }
                    $lastYears[$i]['ledgers'] = $businessLedgerTemps;
                    $lastYears[$i]['name'] = $subaccount->name;
                    $lastYears[$i]['code'] = $subaccount->code;

                    $balances[$i]['total_now'] = $totalNow;
                    $balances[$i]['total_before'] = $totalBefore;
                    $balances[$i]['name'] = $subaccount->name;
                    $balances[$i]['code'] = $subaccount->code;
                    $i++;
                }
            }
        }

        $lost_profit_ledger_now = Businessledger::where('business_id', $business['id'])->whereYear('date','<=', request('year'))
                                    ->whereHas('account', function($query){
                                        $query->where('code', 'like', '4%')
                                                ->orWhere('code', 'like', '5%');
                                    })
                                    ->get();

        $lost_profit_ledger_before = Businessledger::where('business_id', $business['id'])->whereYear('date','<', request('year'))
                                    ->whereHas('account', function($query){
                                        $query->where('code', 'like', '4%')
                                                ->orWhere('code', 'like', '5%');
                                    })
                                    ->get();

        $lost_profit_now = $lost_profit_ledger_now->sum('credit') - $lost_profit_ledger_now->sum('debit');
        $lost_profit_before = $lost_profit_ledger_before->sum('credit') - $lost_profit_ledger_before->sum('debit');
        
        $j = 0;
        $is_there_current_year_earnings_now = false;
        $is_there_current_year_earnings_before = false;


        if (count($lastYears) > 0) {
            foreach ($lastYears as $lastYear) {
                $totalNow = 0;
                $totalBefore = 0;
                foreach ($lastYear['ledgers'] as $ledger) {
                    $time = DateTime::createFromFormat("Y-m-d", $ledger->date);
                    if ($time->format("Y") < request('year')) {
                        if ($lastYear['name'] == 'Laba Tahun Berjalan') {
                            $is_there_current_year_earnings_before = true;
                        }
                        $totalBefore += $ledger->debit - $ledger->credit;
                    }

                    if ($lastYear['name'] == 'Laba Tahun Berjalan') {
                        $is_there_current_year_earnings_now = true;
                    }
                    
                    $totalNow += $ledger->debit - $ledger->credit;
                }
                if ($lastYear['name'] == 'Laba Tahun Berjalan') {
                    $lastYear['total_now'] = $totalNow + $lost_profit_now;
                    $lastYear['total_before'] = $totalBefore + $lost_profit_before;
                } else {
                    $lastYear['total_now'] = $totalNow;
                    $lastYear['total_before'] = $totalBefore;
                }

                $j++;
            }
        }

        if (!$is_there_current_year_earnings_now || !$is_there_current_year_earnings_before) {
            array_push($balances, [
                    "name" => "Laba Tahun Berjalan",
                    "code" => "3299999",
                    "total_now" => -1 * $lost_profit_now,
                    "total_before" => -1 * $lost_profit_before,
            ]);
        }

       $period = Carbon::now()->isoformat(request('year'));
    
        return response()->json([
            'status' => 'success',
            'data' => [
                'balance' => $balances,
                'period' => $period,
                'dates' => request('year'),
            ],
        ]); 
    }
}
