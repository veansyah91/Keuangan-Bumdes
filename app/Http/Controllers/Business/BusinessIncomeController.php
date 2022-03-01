<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\BusinessBalance;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ClosingIncomeActivity;
use App\Models\BusinessBalanceActivity;

class BusinessIncomeController extends Controller
{
    public function index(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $tanggalSekarang = $request->ke;
        $tanggalAkhir = $request->dari;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $berdasarkan = $request->berdasarkan;
        
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

        if ($request->berdasarkan == 'month') {
            $tmp = [];
            $i = 0;
            $tanggalSekarang = Date($request->tahun . '-' . $request->bulan . '-01');

            do {
                $tmp[$i] = date('Y-m-d', strtotime('+' . $i . 'days', strtotime($tanggalSekarang)));
                $dt = Carbon::parse($tmp[$i]);
                $i++;
            } while ($dt->month <= $request->bulan);

            $reverseTmp = array_reverse($tmp);
            array_splice($reverseTmp, 0, 1);
            $tanggal = $reverseTmp;
        }

        $tanggalAkhir = $tanggal[0];
        
        return view('business.business-income.index', compact('business', 'tanggal', 'tanggalSekarang', 'tanggalAkhir', 'bulan', 'tahun', 'berdasarkan'));
    }

    public function updateBusinessBalance(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        // check saldo 
        $balance = BusinessBalance::where('business_id', $business['id'])->first();

        // cek apakah sudah diinput
        $closing = ClosingIncomeActivity::where('business_id', $business['id'])->where('tanggal', $request->tanggal)->first();

        if ($closing) {
            $old = $balance['sisa'] - $closing['jumlah'];

            $balance->update([
                'sisa' => $old + $request->jumlah
            ]);

            $closing->update([
                'jumlah' => $request->jumlah
            ]);

            $activity = BusinessBalanceActivity::where('tanggal', $request->tanggal)
                                                ->where('uang_masuk', '>', 0)
                                                ->first();

            if ($activity) {
                $activity->update([
                    'uang_masuk' => $request->jumlah,
                ]);
            } else {
                BusinessBalanceActivity::create([
                    'business_balance_id' => $balance['id'],
                    'tanggal' => $request->tanggal,
                    'uang_masuk' => $request->jumlah,
                    'uang_keluar' => 0,
                    'business_expense_id' => null,
                    'keterangan' => 'Uang Masuk Harian',
                    'closing_income_activity_id' => $closing['id']
                ]);
            }
        } 
        else {
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
                
            } else {
                $balance = BusinessBalance::create([
                    'business_id' => $business['id'],
                    'sisa' => $request->jumlah,
                ]);
            }
            $businessBalanceActivity = BusinessBalanceActivity::create([
                'business_balance_id' => $balance['id'],
                'tanggal' => $request->tanggal,
                'uang_masuk' => $request->jumlah,
                'uang_keluar' => 0,
                'business_expense_id' => null,
                'keterangan' => 'Uang Masuk Harian',
                'closing_income_activity_id' => $closing['id']
            ]);
        }

        return redirect('/' . $business['id'] . '/business-income?dari=' . $request->dari . '&ke=' . $request->ke)->with('Success', 'Berhasil Memperbaharui Kas');
    }
}
