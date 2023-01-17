<?php

namespace App\Http\Controllers\Business;

use DateTime;
use Carbon\Carbon;
use App\Models\Business;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\SubClassificationAccount;

class BusinessBalanceReportController extends Controller
{
    public function index(Business $business){
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.report.balance.index', compact('business'));
    }

    public function print(Business $business){
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.report.balance.print', [
            'author' => request()->user(),
            'business' => $business,
        ]);
    }

    public function year(Business $business){
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.report.balance.year', compact('business'));
    }

    public function printYear(Business $business){
        $identity = Identity::first();
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
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

        $lost_profit_ledger = Businessledger::where('business_id', $business['id'])->where('business_id', $business['id'])->filter(request(['date_to', 'end_week', 'end_month', 'end_year']))
                                    ->whereHas('account', function($query){
                                        $query->where('code', 'like', '4%')
                                                ->orWhere('code', 'like', '5%');
                                    })
                                    ->get();

        $lost_profit = $lost_profit_ledger->sum('credit') - $lost_profit_ledger->sum('debit');

        $is_there_current_year_earnings = false;

        foreach ($data as  $d) {

            $ledgers = Businessledger::where('business_id', $business['id'])->where('business_id', $business['id'])->filter(request(['date_to', 'end_week', 'end_month', 'end_year']))->where('account_id', $d['id'])->get();

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
        $balanceNow = SubClassificationAccount::where('code','<','4100000')
                                            ->has('businessaccounts')
                                            ->with('businessaccounts', function($query) use ($business){
                                                $query->where('business_id', $business['id'])
                                                        ->where('code','<','4100000')
                                                        ->whereHas('ledgers')       
                                                        ->with('ledgers', function($query) use ($business){
                                                                $query->where('business_id', $business['id'])
                                                                    ->whereYear('date','<=', request('year'));
                                                        });
                                            })
                                            ->get();
                                        
        $reportYear = [];
        $i = 0;

        foreach ($balanceNow as $sub) {
            if (count($sub->businessaccounts) > 0) {
                foreach ($sub->businessaccounts as $businessaccount) {
                    if (count($businessaccount->ledgers) > 0) {
                        $reportYear[$i] = $sub;
                    }
                }
                $i++;
            }
        }

        //laba rugi pada tabel buku besar tahun sekarang
        $lost_profit_ledger_now = Businessledger::where('business_id', $business['id'])->whereYear('date','<=', request('year'))
                                    ->whereHas('account', function($query){
                                        $query->where('code', 'like', '4%')
                                                ->orWhere('code', 'like', '5%');
                                    })
                                    ->get();
        //nilai laba rugi tahun sekarang
        $lost_profit_now = $lost_profit_ledger_now->sum('credit') - $lost_profit_ledger_now->sum('debit');

        //data tabel pada buku besar dengan akun laba
        $lost_profit_ledger_account_profit_now = Businessledger::where('business_id', $business['id'])->whereYear('date','<=', request('year'))
                                            ->whereHas('account', function($query){
                                                $query->where('sub_category','Laba');
                                            })
                                            ->get();
        
        $lost_profit_account_profit_now = $lost_profit_ledger_account_profit_now->sum('credit') - $lost_profit_ledger_account_profit_now->sum('debit');

        //laba rugi pada tabel buku besar tahun sebelumnya
        $lost_profit_ledger_before = Businessledger::where('business_id', $business['id'])->whereYear('date','<', request('year'))
                                    ->whereHas('account', function($query){
                                        $query->where('code', 'like', '4%')
                                                ->orWhere('code', 'like', '5%');
                                    })
                                    ->get();
        //nilai laba rugi tahun sebelumnya
        $lost_profit_before = $lost_profit_ledger_before->sum('credit') - $lost_profit_ledger_before->sum('debit');

        //data tabel pada buku besar dengan akun laba
        $lost_profit_ledger_account_profit_before =Businessledger::where('business_id', $business['id'])->whereYear('date','<', request('year'))
                                                    ->whereHas('account', function($query){
                                                        $query->where('sub_category','Laba');
                                                    })
                                                    ->get();

        $lost_profit_account_profit_before = $lost_profit_ledger_account_profit_before->sum('credit') - $lost_profit_ledger_account_profit_before->sum('debit');

        
        $j = 0;
        $is_there_current_year_earnings_now = false;
        $is_there_current_year_earnings_before = false;

        if (count($reportYear) > 0) {
            foreach ($reportYear as $report) {
                $totalNow = 0;
                $totalBefore = 0;
                foreach ($report->businessaccounts as $account) {
                    foreach ($account->ledgers as $ledger) {
                        $time = DateTime::createFromFormat("Y-m-d", $ledger->date);
                        if ($time->format("Y") < request('year')) {
                            $report['name'] == 'Laba Tahun Berjalan' ? 
                                        $is_there_current_year_earnings_before = true :
                                        $is_there_current_year_earnings_before = false;
                            $totalBefore += $ledger->debit - $ledger->credit;
                        }

                        $report['name'] == 'Laba Tahun Berjalan' ? 
                                        $is_there_current_year_earnings_now = true :
                                        $is_there_current_year_earnings_now = false;
                        
                        $totalNow += $ledger->debit - $ledger->credit;
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
                    "code" => "3299999",
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
            ],
        ]); 
    }
}
