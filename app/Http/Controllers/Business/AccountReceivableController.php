<?php

namespace App\Http\Controllers\Business;

use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Business;
use App\Models\Identity;
use App\Models\BusinessUser;
use Illuminate\Http\Request;
use App\Models\Businessledger;
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
                            $query->whereHas('accountReceivables',  fn($query) => 
                            $query->where('business_id', $business['id']))
                                    ->with('accountReceivables');
                        })
                        ->first();
        
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function lend(Business $business, $id)
    {
        $data = AccountReceivable::whereId($id)
                                    ->with('contact')
                                    ->with('debtSubmission')
                                    ->first();

        $ledger = Businessledger::whereBusinessId($business['id'])
                        ->whereNoRef($data['no_ref'])
                        ->whereDebit('>', 0)
                        ->first();

        $data['account'] = [
            'id' => $ledger['account_id'],
            'name' => $ledger['account_name'],
        ];
        
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
        $contacts = Contact::where('name', 'like', '%' . request('search') . '%')
                        ->whereHas('accountReceivables',  fn($query) => 
                            $query->where('business_id', $business['id']))
                        ->with('accountReceivables',  fn($query) => 
                            $query->where('business_id', $business['id']))
                        ->addSelect([
                            'total_debit' => AccountReceivable::where('business_id', $business['id'])
                                                                ->whereColumn('contact_id', 'contacts.id')
                                                                ->selectRaw('sum(debit)')
                        ])      
                        ->addSelect([
                            'total_credit' => AccountReceivable::where('business_id', $business['id'])
                                                                ->whereColumn('contact_id', 'contacts.id')
                                                                ->selectRaw('sum(credit)')
                        ])     
                        ->paginate(50);

        
        return response()->json([
            'status' => 'success',
            'data' => $contacts,
        ]);
    }

    public function getDataByInvoice(Business $business, $contact)
    {
        $invoices = Invoice::where('contact_id', $contact)
                            ->where('no_ref', 'like', '%'. request('search') .'%')
                            ->whereHas('accountReceivables',  fn($query) => 
                                $query->where('business_id', $business['id'])     
                                )
                            ->with('accountReceivables', fn($query) => 
                                $query->with('creditApplication')
                            )
                            ->addSelect([
                                'total_debit' => AccountReceivable::where('business_id', $business['id'])
                                                                    ->whereColumn('invoice_id', 'invoices.id')
                                                                    ->limit(1)
                                                                    ->selectRaw('sum(debit)')
                            ])      
                            ->addSelect([
                                'total_credit' => AccountReceivable::where('business_id', $business['id'])
                                                                    ->whereColumn('invoice_id', 'invoices.id')
                                                                    ->limit(1)
                                                                    ->selectRaw('sum(credit)')
                            ])    
                            ->addSelect([
                                'tenor' => AccountReceivable::where('business_id', $business['id'])
                                                                    ->whereColumn('invoice_id', 'invoices.id')
                                                                    ->limit(1)
                                                                    ->selectRaw('tenor')
                            ])      
                            ->paginate(50);

        
        return response()->json([
            'status' => 'success',
            'data' => $invoices,
        ]);
    }
}
