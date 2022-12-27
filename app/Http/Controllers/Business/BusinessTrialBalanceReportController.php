<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Business;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Http\Controllers\Controller;

class BusinessTrialBalanceReportController extends Controller
{
    public function index(Business $business){
        return view('business.report.trial-balance.index', compact('business'));
    }

    public function print(Business $business){
        $identity = Identity::first();
        return view('business.report.trial-balance.print', [
            'author' => request()->user(),
            'identity' => $identity,
            'business' => $business,
        ]);
    }

    public function getApiData(Business $business)
    {
        $accounts = Businessaccount::where('business_id', $business['id'])->where('is_active', true)->orderBy('code', 'asc')->get();

        //cari buku besar (legder)
        $ledgers = [];
        $l = 0;
        $total_lost_profit_before = Businessledger::where('business_id', $business['id'])->where('account_code', '>', '4000000')->whereYear('date', '<', request('year'))
                                            ->get();
        foreach ($accounts as $account) {
            if ($account->code < '4100000') {
                $is_ledger = 1;

                
                $ledgerEnd = Businessledger::where('business_id', $business['id'])->where('account_id', $account->id)->whereYear('date','<=',request('year'))
                                                ->get();
                $ledgerStart = Businessledger::where('business_id', $business['id'])->where('account_id', $account->id)->whereYear('date','<=',request('year') - 1)
                                ->get();
                $ledgerMutation = Businessledger::where('business_id', $business['id'])->where('account_id', $account->id)->whereYear('date', request('year'))
                                ->get();

                $total_lost_profit_before = Businessledger::where('business_id', $business['id'])->where('account_code', '>', '4000000')->whereYear('date', '<', request('year'))
                ->get();

                $ledgers[$l]['code'] = $account->code;
                $ledgers[$l]['name'] = $account->name;
                $ledgers[$l]['debit_start'] = $ledgerStart->sum('debit');
                $ledgers[$l]['credit_start'] = $ledgerStart->sum('credit');
                $ledgers[$l]['debit_mutation'] = $ledgerMutation->sum('debit');
                $ledgers[$l]['credit_mutation'] = $ledgerMutation->sum('credit');
                $ledgers[$l]['debit_end'] = $ledgerEnd->sum('debit');
                $ledgers[$l]['credit_end'] = $ledgerEnd->sum('credit');

                if ($account->name == 'Laba Tahun Berjalan') {
                    if ($total_lost_profit_before->sum('debit') - $total_lost_profit_before->sum('credit') > 0) {
                        $ledgers[$l]['debit_start'] = $total_lost_profit_before->sum('debit') - $total_lost_profit_before->sum('credit') - $ledgers[$l]['debit_start'];
                        $ledgers[$l]['debit_end'] = $total_lost_profit_before->sum('debit') - $total_lost_profit_before->sum('credit') - $ledgers[$l]['debit_end'];
                    } else {
                        $ledgers[$l]['credit_start'] = $total_lost_profit_before->sum('credit') - $total_lost_profit_before->sum('debit') - $ledgers[$l]['credit_start'];
                        $ledgers[$l]['credit_end'] = $total_lost_profit_before->sum('credit') - $total_lost_profit_before->sum('debit') - $ledgers[$l]['credit_end'];
                    }
                }
                
                $l++;
            } else{

                $ledgerMutation = Businessledger::where('business_id', $business['id'])->where('account_id', $account->id)->whereYear('date', request('year'))
                                ->get();

                $ledgers[$l]['code'] = $account->code;
                $ledgers[$l]['name'] = $account->name;
                $ledgers[$l]['debit_start'] = 0;
                $ledgers[$l]['credit_start'] = 0;
                $ledgers[$l]['debit_mutation'] = $ledgerMutation->sum('debit');
                $ledgers[$l]['credit_mutation'] = $ledgerMutation->sum('credit');
                $ledgers[$l]['debit_end'] = $ledgerMutation->sum('debit');
                $ledgers[$l]['credit_end'] = $ledgerMutation->sum('credit');
                $l++;
            }
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
                'trial_balance' => $ledgers,
                'period' => $period,
                'lost_profit' => $total_lost_profit_before
            ],
        ]); 
    }
}
