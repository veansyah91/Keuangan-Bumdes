<?php

namespace App\Http\Controllers\Business;

use App\Models\Contact;
use App\Models\Business;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\PurchaseGoods;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccountPayableController extends Controller
{
    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        
        $identity = Identity::first();
        return view('business.account-payable.index', compact('business','identity'));
    }

    public function show(Business $business, $id)
    {
        $data = Contact::where('id', $id)
                        ->whereHas('purchaseGoods')
                        ->with('purchaseGoods', function($query) use ($business){
                            $query->whereHas('accountPayables',  fn($query) => 
                            $query->where('business_id', $business['id']))
                                    ->with('accountPayables');
                        })
                        ->first();
        
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    

    public function getData(Business $business)
    {
        $data = Contact::where('name', 'like', '%' . request('search') . '%')
                        ->whereHas('accountPayables',  fn($query) => 
                        $query
                        ->where('business_id', $business['id']))
                        ->with('accountPayables')
                        ->withSum('accountPayables', 'debit')
                        ->withSum('accountPayables', 'credit')
                        ->paginate(50);

        
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function getDataByPurchaseGoods(Business $business, $contact)
    {
        $data = PurchaseGoods::where('contact_id', $contact)
                        ->whereHas('accountPayables',  fn($query) => 
                        $query->where('business_id', $business['id']))
                        ->with('accountPayables')
                        ->withSum('accountPayables', 'debit')
                        ->withSum('accountPayables', 'credit')
                        ->paginate(50);

        
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
