<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\BusinessBalance;
use App\Http\Controllers\Controller;
use App\Models\ClosingIncomeActivity;
use App\Models\BusinessBalanceActivity;

class BusinessIncomeController extends Controller
{
    public function index(Business $business, Request $request)
    {
        $tanggalSekarang = $request->ke;
        $tanggalAkhir = $request->dari;
        
        if (!$request->ke)
        {
            $tanggalSekarang = Date('Y-m-d');
        }

        if (!$request->dari) {
            $tanggalAkhir = date('Y-m-d', strtotime('-6 days', strtotime($tanggalSekarang)));
        }        

        $tanggal = [];

        $i = 0;
        $tanggal[$i] = $tanggalSekarang;
        while ($tanggal[$i] > $tanggalAkhir) {
            $i++;
            $tanggal[$i] = date('Y-m-d', strtotime('-' . $i . 'days', strtotime($tanggalSekarang)));
        }

        return view('business.business-income.index', compact('business', 'tanggal'));
    }

    public function updateBusinessBalance(Business $business, Request $request)
    {
        // check saldo 
        $balance = BusinessBalance::where('business_id', $business['id'])->first();

        $closing = ClosingIncomeActivity::create([
            'tanggal' => $request->tanggal,
            'business_id' => $business['id'],
            'jumlah' => $request->jumlah
        ]);

        if ($balance) {
            $old = $balance['sisa'];
            $balance->update([
                'sisa' => $old + $request->jumlah
            ]);

            $businessBalanceActivity = BusinessBalanceActivity::create([
                'business_balance_id' => $balance['id'],
                'tanggal' => $request->tanggal,
                'uang_masuk' => $request->jumlah,
                'uang_keluar' => 0,
                'business_expense_id' => null,
                'keterangan' => 'Uang Masuk Harian'
            ]);
            
        } else {
            $balance = BusinessBalance::create([
                'business_id' => $business['id'],
                'sisa' => $request->jumlah,
            ]);

            $businessBalanceActivity = BusinessBalanceActivity::create([
                'business_balance_id' => $balance['id'],
                'tanggal' => $request->tanggal,
                'uang_masuk' => $request->jumlah,
                'uang_keluar' => 0,
                'business_expense_id' => null,
                'keterangan' => 'Uang Masuk Harian'
            ]);
        }
        

        return redirect('/' . $business['id'] . '/business-income?dari=' . $request->dari . '&ke=' . $request->ke)->with('Success', 'Berhasil Memperbaharui Kas');
    }
}
