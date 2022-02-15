<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Asset;
use App\Models\Business;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\BusinessBalance;
use App\Models\BusinessExpense;
use App\Http\Controllers\Controller;
use App\Models\ClosingIncomeActivity;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    public function index(Business $business)
    {
        $businessBalance = BusinessBalance::where('business_id', $business['id'])->first();

        $now = Date('Y-m');

        $months = [];

        $varMonths = [];

        $j = 0;
        for ($i=6; $i >= 0; $i--) { 

            $varMonths[$j] = date('Y-m', strtotime('-' . $i . 'month', strtotime($now)));
            $m = Carbon::parse($varMonths[$j])->locale('id');
            $months[$j] = $m->translatedFormat('F Y');
            $j++;
        }  

        $expenses = [];
        foreach ($varMonths as $key => $varMonth) {
            $dt = Carbon::parse($varMonth);
            $expenses[$key] = BusinessExpense::where('business_id', $business['id'])
                                                ->whereMonth('tanggal_keluar', $dt->month)
                                                ->whereYear('tanggal_keluar', $dt->year)
                                                ->get()
                                                ->sum('jumlah');
        }
        $incomes = [];
        foreach ($varMonths as $key => $varMonth) {
            $dt = Carbon::parse($varMonth);
            $incomes[$key] = ClosingIncomeActivity::where('business_id', $business['id'])
                                                ->whereMonth('tanggal', $dt->month)
                                                ->whereYear('tanggal', $dt->year)
                                                ->get()
                                                ->sum('jumlah');
        }

        $getAsset = Asset::where('business_id', $business['id'])->get();
        $sumAsset = $getAsset->sum(function ($query){
            return $query['harga_satuan'] * $query['jumlah_bagus'];
        });

        $products = Product::query()->whereHas('stock', function($query){
                        $query->where('jumlah', '>', 0);
                    })           
                    ->with('stock')             
                    ->where('business_id', $business['id'])
                    ->orderBy('created_at')
                    ->orderBy('kategori')
                    ->get();

        $total = 0;

        foreach ($products as $key => $product) {
            $total += $product->modal * $product->stock->jumlah;
        }

        return view('business.dashboard.index', compact('business', 'businessBalance', 'sumAsset', 'stock'));
    }

    public function cashflow(Business $business)
    {
        $now = Date('Y-m');

        $months = [];

        $varMonths = [];

        $j = 0;
        for ($i=6; $i >= 0; $i--) { 

            $varMonths[$j] = date('Y-m', strtotime('-' . $i . 'month', strtotime($now)));
            $m = Carbon::parse($varMonths[$j])->locale('id');
            $months[$j] = $m->translatedFormat('F Y');
            $j++;
        }  

        $expenses = [];
        $incomes = [];
        $profits = [];
        foreach ($varMonths as $key => $varMonth) {
            $dt = Carbon::parse($varMonth);
            $expenses[$key] = BusinessExpense::where('business_id', $business['id'])
                                                ->whereMonth('tanggal_keluar', $dt->month)
                                                ->whereYear('tanggal_keluar', $dt->year)
                                                ->get()
                                                ->sum('jumlah');

            $incomes[$key] = ClosingIncomeActivity::where('business_id', $business['id'])
                                                ->whereMonth('tanggal', $dt->month)
                                                ->whereYear('tanggal', $dt->year)
                                                ->get()
                                                ->sum('jumlah');
            
            $profits[$key] = $incomes[$key] - $expenses[$key];
        }

        $data = [
            'label' => $months,
            'expenses' => $expenses,
            'incomes' => $incomes,
            'profits' => $profits,
        ];

        $response = [
            'message' => "Berhasil Mendapatkan Data",
            'status' => 'Sucess',
            'data' => $data
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }
}
