<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\AccountReceivable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountReceivablePayment;
use Symfony\Component\HttpFoundation\Response;

class AccountReceivableController extends Controller
{
    public function index(Business $business)
    {
        $accountReceivables = AccountReceivable::orderBy('created_at', 'desc')->orderBy('sisa', 'desc')->paginate(10);
        $sumAccountReceivable = AccountReceivable::all()->sum('sisa');
        return view('business.account-receivable.index', compact('business' ,'accountReceivables', 'sumAccountReceivable'));
    }

    public function detail(Business $business, AccountReceivable $accountReceivable)
    {
        $data = [
            'accountReceivable' => $accountReceivable,
            'payment' => AccountReceivablePayment::whereAccountReceivableId($accountReceivable['id'])->get()
        ];
        
        $response = [
            'message' => "Berhasil Mendapatkan Data",
            'status' => "Success",
            'data' => $data
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function store(Business $business, Request $request)
    {
        $user = Auth::user();
        AccountReceivablePayment::create([
            'account_receivable_id' => $request->accountReceivable,
            'jumlah_bayar' => $request->jumlah_bayar,
            'operator' => $user['name']
        ]);

        // kurangi sisa piutang
        $accountReceivable = AccountReceivable::find($request->accountReceivable);
        $jumlah = $accountReceivable['sisa'] - $request->jumlah_bayar;
        $accountReceivable->update([
            'sisa' => $jumlah
        ]);

        return redirect('/' . $business['id'] . '/account-receivable');
    }
}
