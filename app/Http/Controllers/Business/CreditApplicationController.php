<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\AccountReceivable;
use App\Models\CreditApplication;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreditApplicationController extends Controller
{
    public function noRefCreditApplicationRecomendation(Business $business){
        $fix_ref_no = '';

        $date = str_replace('-', '', request('date'));

        $endCreditApplication = CreditApplication::where('business_id', $business['id'])
                            ->where('no_ref', 'like', 'CA-'. $date . '%')
                            ->orderBy('id', 'desc')
                            ->first();

        $newCreditApplication = 'CA-' . $date . '0001';

        if ($endCreditApplication) {
            $split_end_invoice = explode('-', $endCreditApplication['no_ref']);

            $newNumber = (int)$split_end_invoice[1] + 1;

            $newCreditApplication = 'CA-' . $newNumber;
        }

        return response()->json([
            'status' => 'success',
            'data' => $newCreditApplication,
        ]);
    }

    public function getData(Business $business)
    {
        return response()->json([
            'status' => 'success',
            'data' => CreditApplication::filter(request(['date_from','date_to','this_week','this_month','this_year', 'search']))
                                ->whereBusinessId($business['id'])
                                ->status()
                                ->with('contact')
                                ->with('product')
                                ->latest()
                                ->paginate(50),
        ]);
    }

    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.credit-application.index', ['business' => $business, ]);
    }

    public function store(Business $business, Request $request)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'tenor' => 'required|numeric',
            'profit' => 'required|numeric',
            'installment' => 'required|numeric',
            'value' => 'required|numeric',
            'other_cost' => 'numeric',
            'downpayment' => 'numeric',
        ]); 

        if (!$request->contact['id']) {
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        if (!$request->product['id']) {
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        $attributes['contact_id'] = $request->contact['id'];
        $attributes['business_id'] = $request->business['id'];
        $attributes['contact_name'] = $request->contact['name'];
        $attributes['author'] = $request->user()->name;
        $attributes['unit_price'] = $request->product['unit_price'];
        $attributes['product_id'] = $request->product['id'];

        $debtSubmission = CreditApplication::create($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    public function show(Business $business, $id)
    {
        $data = CreditApplication::whereId($id)->with('contact')->with('product')->first();
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function update(Request $request, Business $business, $id)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'tenor' => 'required|numeric',
            'profit' => 'required|numeric',
            'installment' => 'required|numeric',
            'value' => 'required|numeric',
            'other_cost' => 'numeric',
            'downpayment' => 'numeric',
            'status' => 'required|string',
        ]); 

        if (!$request->contact['id']) {
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        if (!$request->product['id']) {
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        $attributes['contact_id'] = $request->contact['id'];
        $attributes['business_id'] = $request->business['id'];
        $attributes['contact_name'] = $request->contact['name'];
        $attributes['author'] = $request->user()->name;
        $attributes['unit_price'] = $request->product['unit_price'];
        $attributes['product_id'] = $request->product['id'];

        $debtSubmission = CreditApplication::find($id);
        
        $debtSubmission->update($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    public function destroy(Business $business, $id)
    {
        $data = CreditApplication::findOrFail($id);

        //cek apakah data telah digunakan
        $accountReceivable = AccountReceivable::where('credit_application_id', $data['id'])->first();

        if ($accountReceivable) {
            throw ValidationException::withMessages([
                'message' => 'Data Tidak Bisa Dihapus, Data Telah Digunakan'
            ]);
        }

        $data->delete();
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
