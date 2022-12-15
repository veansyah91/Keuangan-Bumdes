<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ledger;
use App\Models\Account;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\SubClassificationAccount;

class LostProfitReportController extends Controller
{
    public function index(){
        return view('admin.report.lost-profit.index');
    }

    public function year(){
        return view('admin.report.lost-profit.year');
    }

    public function print(){
        $identity = Identity::first();
        return view('admin.report.lost-profit.print', [
            'author' => request()->user(),
            'identity' => $identity
        ]);
    }

    public function printYear(){
        $identity = Identity::first();
        return view('admin.report.lost-profit.print-year', [
            'author' => request()->user(),
            'identity' => $identity
        ]);
    }

    public function getApiData(){
        $data = Account::where('code','>','3999999')
                        ->whereHas('ledgers')
                        ->orderBy('code', 'asc')
                        ->get();

        foreach ($data as  $d) {
            $ledgers = Ledger::filter(request(['date_to','date_from', 'this_week', 'this_month', 'this_year']))->where('account_id', $d['id'])->get();

            $d['total'] = $ledgers->sum('debit') - $ledgers->sum('credit');
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
                'lost_profit' => $data,
                'period' => $period,
            ],
        ]); 
    }

    public function getApiDataYear(){
        $lost_profit_nows = SubClassificationAccount::where('code','>','3999999')
                                                    ->with('accounts')
                                                    ->get();
        
        
        foreach ($lost_profit_nows as  $lost_profit_now) {
            $totalNow = 0;
            $totalBefore = 0;
            foreach ($lost_profit_now->accounts as $account) {
                $ledger_nows = Ledger::whereYear('date', request('year'))->where('account_id', $account['id'])->get();
                $ledger_befores = Ledger::whereYear('date', request('year') - 1)->where('account_id', $account['id'])->get();

                if (count($ledger_nows) > 0) {
                    $totalNow += $ledger_nows->sum('debit') - $ledger_nows->sum('credit');
                }
                if (count($ledger_befores) > 0) {
                    $totalBefore += $ledger_befores->sum('debit') - $ledger_befores->sum('credit');
                }
                
            }
            $lost_profit_now['total_now'] = $totalNow; 
            $lost_profit_now['total_before'] = $totalBefore; 
        }

        $period = Carbon::now()->isoformat(request('year'));
    
        return response()->json([
            'status' => 'success',
            'data' => [
                'lost_profit' => $lost_profit_nows,
                'period' => $period,
            ],
        ]); 
    }
}
