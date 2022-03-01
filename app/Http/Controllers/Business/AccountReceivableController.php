<?php

namespace App\Http\Controllers\Business;

use App\Models\Invoice;
use App\Models\Business;
use App\Models\Identity;
use App\Models\BusinessUser;
use Illuminate\Http\Request;
use App\Models\AccountReceivable;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountReceivablePayment;
use Symfony\Component\HttpFoundation\Response;

class AccountReceivableController extends Controller
{
    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        $accountReceivables = AccountReceivable::where('business_id', $business['id'])->orderBy('created_at', 'desc')->orderBy('sisa', 'desc')->paginate(10);
        $sumAccountReceivable = AccountReceivable::all()->sum('sisa');
        $identity = Identity::first();
        return view('business.account-receivable.index', compact('business' ,'accountReceivables', 'sumAccountReceivable', 'identity'));
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
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }

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

    public function payLater(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        $accountReceivables = AccountReceivable::where('business_id', $business['id'])->where('sisa', '>', 0)->orderBy('created_at', 'desc')->orderBy('sisa', 'desc')->get();
        $identity = Identity::first();
        return view('business.account-receivable.pay-later', compact('business', 'accountReceivables', 'identity'));
    }

    public function payLaterList(Business $business)
    {
        $accountReceivables = AccountReceivable::where('business_id', $business['id'])->where('sisa', '>', 0)->orderBy('created_at', 'desc')->orderBy('sisa', 'desc')->get();

        $data = [
            "accountReceivables" => $accountReceivables
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

    public function payLaterDetail($id)
    {   
        
        $accountReceivable = AccountReceivable::find($id);

        $invoice = Invoice::find($accountReceivable['invoice_id']);
        $details = $invoice->products;

        $data = [
            'accountReceivable' => $accountReceivable,
            'invoice' => $invoice,
        ];

        $response = [
            'message' => "Berhasil Mendapatkan Data",
            'status' => 200,
            'data' => $data
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }
}
