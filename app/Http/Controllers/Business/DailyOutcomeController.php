<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\BusinessBalance;
use App\Models\BusinessExpense;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\BusinessBalanceActivity;
use Symfony\Component\HttpFoundation\Response;


class DailyOutcomeController extends Controller
{
    public function index(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $tanggal_awal = $request['tanggal_awal'];
        $tanggal_akhir = $request['tanggal_akhir'];

        $expenses = ($tanggal_awal && $tanggal_akhir) 
                    ? BusinessExpense::whereBetween('tanggal_keluar', [$tanggal_awal, $tanggal_akhir])->where('business_id', $business['id'])->orderBy('tanggal_keluar', 'desc')->paginate(10)
                    : BusinessExpense::where('business_id', $business['id'])->orderBy('tanggal_keluar', 'desc')->paginate(10);

        return view('business.daily-outcome.index', compact('business', 'expenses'));
    }

    public function store(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $user = Auth::user();
        $expense = BusinessExpense::create([
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'tanggal_keluar' => $request->tanggal,
            'business_id' => $business['id'],
            'operator' => $user['name']
        ]);

        if ($request->kas) {
            $businessBalance = BusinessBalance::where('business_id', $business['id'])->first();
            // jika ada kas 
            if ($businessBalance) {
                $old = $businessBalance['sisa'];

                $businessBalance->update([
                    'sisa' => $old - $request->jumlah
                ]);
            } else {
                $businessBalance = BusinessBalance::create([
                    'sisa' => $request->jumlah,
                    'business_id' => $business['id']
                ]);
            }            

            $businessBalanceActivity = BusinessBalanceActivity::create([
                'business_balance_id' => $businessBalance['id'],
                'tanggal' => $request->tanggal,
                'uang_masuk' => null,
                'uang_keluar' => $request->jumlah,
                'keterangan' => $request->keterangan,
                'business_expense_id' => $expense['id']
            ]);
        }

        return redirect('/' . $business['id'] . '/expense')->with('Success', 'Berhasil Menambah Data Pengeluaran');
    }

    public function update(Business $business, BusinessExpense $expense, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $expense->update([
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'tanggal_keluar' => $request->tanggal,
        ]);

        $businessIncomeActivity =BusinessBalanceActivity::where('business_expense_id', $expense['id'])->first();
        if ($businessIncomeActivity) {
            // saldo lama 
            $businessBalance = BusinessBalance::where('business_id', $business['id'])->first();
            $old = $businessBalance['sisa'] + $businessIncomeActivity['uang_keluar'];

            $businessBalance->update([
                'sisa' => $old - $request->jumlah,
            ]);

            $businessIncomeActivity->update([
                "uang_keluar" => $request->jumlah,
                'tanggal' => $request->tanggal,
            ]);
        }
        
        return redirect('/' . $business['id'] . '/expense')->with('Success', 'Berhasil Mengubah Data Pengeluaran');
    }

    public function delete(Business $business, BusinessExpense $expense)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $businessIncomeActivity = BusinessBalanceActivity::where('business_expense_id', $expense['id'])->first();

        if ($businessIncomeActivity) {
            $businessBalance = BusinessBalance::where('business_id', $business['id'])->first();
            $old = $businessBalance['sisa'];
            $businessBalance->update([
                'sisa' => $old + $businessIncomeActivity['uang_keluar']
            ]);
            $businessIncomeActivity->delete();
        }

        $expense->delete();
        return redirect('/' . $business['id'] . '/expense')->with('Success', 'Berhasil Menghapus Data Pengeluaran');
    }
    
    public function detail(BusinessExpense $expense)
    {
        $response = [
            'message' => "Berhasil Mendapatkan Data",
            'status' => 'Sucess',
            'data' => $expense
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function apiValidate(Request $request)
    {
        // validasi disini 
        $validated = $request->validate([
            'jumlah' => 'required|numeric',
            'keterangan' => 'required',
            'tanggal' => 'required|date',
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
}
