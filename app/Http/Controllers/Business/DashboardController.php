<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Asset;
use App\Models\Product;
use App\Models\Business;
use Illuminate\Support\Arr;
use App\Models\BusinessUser;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\BusinessBalance;
use App\Models\BusinessExpense;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ClosingIncomeActivity;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    public function index(Business $business)
    {
        return view('business.dashboard.index', compact('business'));
    }

    public function lostProfit(Business $business)
    {
        $data = Businessledger::where('business_id', $business['id'])->filter(request(['outlet_id', 'month', 'year']))
                        ->whereHas('account', function($query){
                            $query->where('code', 'like', '4%')
                                  ->orWhere('code', 'like', '5%');
                        })
                        ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                        'lost_profit' => $data->sum('credit') - $data->sum('debit'),
                        'income' => $data->sum('credit'),
                        'expense' => $data->sum('debit')
                      ] 
        ]);
    }

    public function asset(Business $business)
    {
        $data = Businessledger::where('business_id', $business['id'])->whereDate('date', '<',  request('time_limit'))
                        ->whereHas('account', function($query){
                            $query->where('code', 'like', '1%');
                        })
                        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data->sum('debit') - $data->sum('credit')
        ]);
    }

    public function liability(Business $business)
    {
        $data = Businessledger::where('business_id', $business['id'])->whereDate('date', '<',  request('time_limit'))
                        ->whereHas('account', function($query){
                            $query->where('code', 'like', '2%');
                        })
                        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data->sum('credit') - $data->sum('debit')
        ]);
    }

    public function equity(Business $business)
    {
        $data = Businessledger::where('business_id', $business['id'])->whereDate('date', '<',  request('time_limit'))
                        ->whereHas('account', function($query){
                            $query->where('code', 'like', '3%');
                        })
                        ->get();

        $lost_profit_ledger = Businessledger::where('business_id', $business['id'])->whereDate('date', '<',  request('time_limit'))
                                    ->whereHas('account', function($query){
                                        $query->where('code', 'like', '4%')
                                                ->orWhere('code', 'like', '5%');
                                    })
                                    ->get();

        $lost_profit_value = $lost_profit_ledger->sum('credit') - $lost_profit_ledger->sum('debit');

        return response()->json([
            'status' => 'success',
            'data' => $data->sum('credit') - $data->sum('debit') + $lost_profit_value,
        ]);
    }
}
