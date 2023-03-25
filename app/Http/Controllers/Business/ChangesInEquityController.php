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

class ChangesInEquityController extends Controller
{
    public function index(Business $business){
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.report.changes-in-equity.index', compact('business'));
    }

    public function print(Business $business){
        $identity = Identity::first();
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.report.changes-in-equity.print', [
            'author' => request()->user(),
            'business' => $business,
            'identity' => $identity,
        ]);
    }

    public function getApiData(Business $business){
        $balanceNow = SubClassificationAccount::where('code','like','3%')
                                            ->with('businessaccounts', fn($query) =>
                                                $query->whereBusinessId($business['id'])
                                                        ->with('ledgers', fn($query)=>
                                                            $query->whereYear('date', request('year'))
                                                        )
                                            )
                                            ->orderBy('code')
                                            ->get();

        $equityBefore = SubClassificationAccount::where('code','like','3%')
                                            ->where('code','<=','3500000')
                                            ->with('businessaccounts', fn($query) =>
                                                $query->whereBusinessId($business['id'])
                                                        ->with('ledgers', fn($query)=>
                                                            $query->whereYear('date', '<' ,request('year'))
                                                        )
                                            )
                                            ->orderBy('code')
                                            ->get();

        $lostProfitBefore = SubClassificationAccount::where('code','like','3%')
                                            ->where('code','>','3500000')
                                            ->with('businessaccounts', fn($query) =>
                                                $query->whereBusinessId($business['id'])
                                                        ->with('ledgers', fn($query)=>
                                                            $query->whereYear('date', '<' ,request('year'))
                                                        )
                                            )
                                            ->orderBy('code')
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
        $lost_profit_ledger = Businessledger::where('business_id', $business['id'])
                                                ->whereYear('date', request('year'))
                                                ->whereHas('account', function($query){
                                                    $query->where('code', 'like', '4%')
                                                            ->orWhere('code', 'like', '5%');
                                                })
                                                ->get();

        //nilai laba rugi tahun sekarang
        $lost_profit = $lost_profit_ledger->sum('credit') - $lost_profit_ledger->sum('debit');

        return response()->json([
            'status' => 'success',
            'data' => [
                'balance' => $balanceNow,
                'equityBefore' => $equityBefore,
                'lostProfitBefore' => $lostProfitBefore,
                'period' => Carbon::now()->isoformat(request('year')),
                'lostProfit' => $lost_profit 
            ],
        ]); 
    }
}
