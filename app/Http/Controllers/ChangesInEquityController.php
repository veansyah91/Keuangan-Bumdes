<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ledger;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\SubClassificationAccount;

class ChangesInEquityController extends Controller
{
    public function index(){
        return view('admin.report.changes-in-equity.index');
    }

    public function print(){
        $identity = Identity::first();
        return view('admin.report.changes-in-equity.print', [
            'author' => request()->user(),
            'identity' => $identity,
        ]);
    }

    public function getApiData(){
        $balanceNow = SubClassificationAccount::where('code','like','3%')
                                            ->with('accounts', fn($query) =>
                                                $query->with('ledgers', fn($query)=>
                                                            $query->whereYear('date', request('year'))
                                                        )
                                            )
                                            ->orderBy('code')
                                            ->get();

        $equityBefore = SubClassificationAccount::where('code','like','3%')
                                            ->where('code','<=','3500000')
                                            ->with('accounts', fn($query) =>
                                                $query->with('ledgers', fn($query)=>
                                                            $query->whereYear('date', '<' ,request('year'))
                                                        )
                                            )
                                            ->orderBy('code')
                                            ->get();

        $lostProfitBefore = SubClassificationAccount::where('code','like','3%')
                                            ->where('code','>','3500000')
                                            ->with('accounts', fn($query) =>
                                                $query->with('ledgers', fn($query)=>
                                                            $query->whereYear('date', '<' ,request('year'))
                                                        )
                                            )
                                            ->orderBy('code')
                                            ->get();

        //laba rugi pada tabel buku besar tahun sekarang
        $lost_profit_ledger = Ledger::whereYear('date', request('year'))
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
