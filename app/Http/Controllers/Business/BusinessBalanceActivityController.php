<?php

namespace App\Http\Controllers\Business;

use App\Models\Income;
use App\Models\Outcome;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\BusinessBalance;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\BusinessBalanceActivity;
use Symfony\Component\HttpFoundation\Response;

class BusinessBalanceActivityController extends Controller
{
    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $businessBalanceActivities = BusinessBalanceActivity::whereHas('businessBalance', function($query) use ($business){
            $query->where('business_id', $business['id']);
        })
        ->orderBy('tanggal', 'desc')
        ->paginate(20);
        return view('business.dashboard.business-balance-activity', compact('businessBalanceActivities', 'business'));
    }

    public function store(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $user = Auth::user();
        $businessBalance = BusinessBalance::where('business_id', $business['id'])->first();

        $activity = BusinessBalanceActivity::create([
            'business_balance_id' => $businessBalance['id'],
            'tanggal' => $request->tanggal,
            'uang_masuk' => $request->uang_masuk,
            'uang_keluar' => $request->uang_keluar,
            'keterangan' => $request->keterangan,
            'bumdes' => true
        ]);

        if ($request->uang_masuk > 0) {
            $old = $businessBalance['sisa'];
            $businessBalance->update([
                'sisa' => $old + $request->uang_masuk
            ]);

            // tambahkan data ke outcomes
            $outcome = Outcome::create([
                'jumlah' => $request->uang_masuk,
                'tanggal_keluar' => $request->tanggal,
                'operator' => $user['name'],
                'business_id' => $business['id'],
                'keterangan' => 'Tambah Modal Ke Unit Usaha ' . $business['nama']
            ]);

        }

        if ($request->uang_keluar > 0) {
            $old = $businessBalance['sisa'];
            $businessBalance->update([
                'sisa' => $old - $request->uang_keluar
            ]);

            // tambahkan data ke outcomes
            $income = Income::create([
                'jumlah' => $request->uang_keluar,
                'tanggal_masuk' => $request->tanggal,
                'operator' => $user['name'],
                'business_id' => $business['id'],
                'keterangan' => 'Penjualan Dari Unit Usaha ' . $business['nama']
            ]);
        }

        return redirect('/' . $business['id'] . '/dashboard/business-balance-activity')->with('Success', 'Berhasil Memperbaharui Aliran Kas');
    }

    public function apiValidate(Request $request)
    {
        // validasi disini 
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required',
            'uangMasuk' => 'numeric|required',
            'uangKeluar' => 'numeric|required',
        ]);

        $response = [
            'message' => "Data Telah Tervalidasi",
            'status' => 1,
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function detail(Business $business, BusinessBalanceActivity $businessBalanceActivity)
    {
        $response = [
            'message' => "Berhasil Mendapatkan Data",
            'status' => "Success",
            'data' => $businessBalanceActivity
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function update(Business $business, BusinessBalanceActivity $businessBalanceActivity, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        // update saldo bisnis
        if ($request->uang_masuk > 0) {
            $businessBalance = BusinessBalance::where('business_id', $business['id'])->first();
            $old = $businessBalance['sisa'] - $businessBalanceActivity['uang_masuk'];
            $businessBalance->update([
                'sisa' => $old + $request->uang_masuk
            ]);

            $outcome = Outcome::where('tanggal_keluar', $businessBalanceActivity['tanggal'])
                                ->where('business_id', $business['id'])
                                ->where('jumlah', $businessBalanceActivity['uang_masuk'])
                                ->first();

            $outcome->update([
                'jumlah' => $request->uang_masuk,
                'tanggal_keluar' => $request->tanggal
            ]);
        }

        if ($request->uang_keluar > 0) {
            $businessBalance = BusinessBalance::where('business_id', $business['id'])->first();

            $old = $businessBalance['sisa'] + $businessBalanceActivity['uang_keluar'];

            $businessBalance->update([
                'sisa' => $old - $request->uang_keluar
            ]);

            $income = Income::where('tanggal_masuk', $businessBalanceActivity['tanggal'])
                                ->where('business_id', $business['id'])
                                ->where('jumlah', $businessBalanceActivity['uang_keluar'])
                                ->first();

            $income->update([
                'jumlah' => $request->uang_keluar,
                'tanggal_masuk' => $request->tanggal
            ]);
            
        }

        $businessBalanceActivity->update([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'uang_masuk' => $request->uang_masuk,
            'uang_keluar' => $request->uang_keluar,
        ]);

        return redirect('/' . $business['id'] . '/dashboard/business-balance-activity')->with('Success', 'Berhasil Memperbaharui Aliran Kas');
    }

    public function delete(Business $business, BusinessBalanceActivity $businessBalanceActivity)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $businessBalance = BusinessBalance::where('business_id', $business['id'])->first();
        
        $old = $businessBalance['sisa'];

        if ($businessBalanceActivity['uang_masuk'] > 0) {
            $businessBalance->update([
                'sisa' => $old - $businessBalanceActivity['uang_masuk']
            ]);

            $outcome = Outcome::where('tanggal_keluar', $businessBalanceActivity['tanggal'])
                                ->where('business_id', $business['id'])
                                ->where('jumlah', $businessBalanceActivity['uang_masuk'])
                                ->first();

            $outcome->delete();
        }

        if ($businessBalanceActivity['uang_keluar'] > 0) {
            $businessBalance->update([
                'sisa' => $old + $businessBalanceActivity['uang_keluar']
            ]);

            $income = Income::where('tanggal_masuk', $businessBalanceActivity['tanggal'])
                                ->where('business_id', $business['id'])
                                ->where('jumlah', $businessBalanceActivity['uang_keluar'])
                                ->first();

            $income->delete();
        }

        $businessBalanceActivity->delete();

        return redirect('/' . $business['id'] . '/dashboard/business-balance-activity')->with('Success', 'Berhasil Menghapus Aliran Kas');
    }
}
