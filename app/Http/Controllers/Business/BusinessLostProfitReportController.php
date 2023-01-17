<?php

namespace App\Http\Controllers\Business;

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

class BusinessLostProfitReportController extends Controller
{
    public function index(Business $business){
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.report.lost-profit.index', compact('business'));
    }

    public function year(Business $business){
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.report.lost-profit.year', compact('business'));
    }

    public function print(Business $business){
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        $identity = Identity::first();
        return view('business.report.lost-profit.print', [
            'author' => request()->user(),
            'identity' => $identity,
            'business' => $business,
        ]);
    }

    public function printYear(Business $business){
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        $identity = Identity::first();
        return view('business.report.lost-profit.print-year', [
            'author' => request()->user(),
            'identity' => $identity,
            'business' => $business
        ]);
    }

    public function getApiData(Business $business){
        $data = Businessaccount::where('business_id', $business['id'])->where('code','>','3999999')
                        ->where('name', '!=', 'Ikhtisar Laba Rugi')
                        ->whereHas('ledgers')
                        ->orderBy('code', 'asc')
                        ->get();

        foreach ($data as  $d) {
            $ledgers = Businessledger::where('business_id', $business['id'])->filter(request(['date_to','date_from', 'this_week', 'this_month', 'this_year']))->where('account_id', $d['id'])->get();

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

    public function getApiDataYear(Business $business){
        $lost_profit_nows = SubClassificationAccount::where('code','>','3999999')
                                                    ->where('name', '!=', 'Ikhtisar Laba Rugi')
                                                    ->with('accounts')
                                                    ->get();
        
        
        foreach ($lost_profit_nows as  $lost_profit_now) {
            $totalNow = 0;
            $totalBefore = 0;
            foreach ($lost_profit_now->businessaccounts as $account) {
                $ledger_nows = Businessledger::where('business_id', $business['id'])->whereYear('date', request('year'))->where('account_id', $account['id'])->get();
                $ledger_befores = Businessledger::where('business_id', $business['id'])->whereYear('date', request('year') - 1)->where('account_id', $account['id'])->get();

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
