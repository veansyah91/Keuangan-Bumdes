<?php

namespace App\Http\Controllers\Business;

use App\Models\Contact;
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
        
        $identity = Identity::first();
        return view('business.account-receivable.index', compact('business','identity'));
    }

    public function show(Business $business, $id)
    {
        $data = Contact::where('id', $id)
                        ->whereHas('invoices')
                        ->with('invoices', function($query) use ($business){
                            $query->whereHas('accountReceivables')
                                    ->with('accountReceivables');
                        })
                        ->first();
        
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
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

    public function getData(Business $business)
    {
        $data = Contact::where('name', 'like', '%' . request('search') . '%')
                        ->whereHas('accountReceivables')
                        ->with('accountReceivables')
                        ->withSum('accountReceivables', 'debit')
                        ->withSum('accountReceivables', 'credit')
                        ->paginate(50);

        
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function getDataByInvoice(Business $business, $contact)
    {
        $data = Invoice::where('contact_id', $contact)
                        ->whereHas('accountReceivables')
                        ->with('accountReceivables')
                        ->withSum('accountReceivables', 'debit')
                        ->withSum('accountReceivables', 'credit')
                        ->paginate(50);

        
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
