<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){
        return view('admin.dashboard');
    }

    public function lostProfit()
    {
        $data = Ledger::filter(request(['outlet_id', 'month', 'year']))
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

    public function asset()
    {
        $data = Ledger::whereDate('date', '<',  request('time_limit'))
                        ->whereHas('account', function($query){
                            $query->where('code', 'like', '1%');
                        })
                        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data->sum('debit') - $data->sum('credit')
        ]);
    }

    public function liability()
    {
        $data = Ledger::whereDate('date', '<',  request('time_limit'))
                        ->whereHas('account', function($query){
                            $query->where('code', 'like', '2%');
                        })
                        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data->sum('credit') - $data->sum('debit')
        ]);
    }

    public function equity()
    {
        $data = Ledger::whereDate('date', '<',  request('time_limit'))
                        ->whereHas('account', function($query){
                            $query->where('code', 'like', '3%');
                        })
                        ->get();

        $lost_profit_ledger = Ledger::whereDate('date', '<',  request('time_limit'))
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
