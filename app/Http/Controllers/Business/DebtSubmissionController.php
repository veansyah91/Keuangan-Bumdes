<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\DebtSubmission;
use App\Models\AccountReceivable;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DebtSubmissionController extends Controller
{
    public function noRefDebtSubmissionRecomendation(Business $business){
        $fix_ref_no = '';

        $date = str_replace('-', '', request('date'));

        $endDebtSubmission = DebtSubmission::where('business_id', $business['id'])
                            ->where('no_ref', 'like', 'DS-'. $date . '%')
                            ->orderBy('id', 'desc')
                            ->first();

        $newAccountPayable = 'DS-' . $date . '0001';

        if ($endDebtSubmission) {
            $split_end_invoice = explode('-', $endDebtSubmission['no_ref']);

            $newNumber = (int)$split_end_invoice[1] + 1;

            $newAccountPayable = 'DS-' . $newNumber;
        }

        return response()->json([
            'status' => 'success',
            'data' => $newAccountPayable,
        ]);
    }

    public function getData(Business $business)
    {
        return response()->json([
            'status' => 'success',
            'data' => DebtSubmission::filter(request(['date_from','date_to','this_week','this_month','this_year', 'search']))
                                ->status()
                                ->with('contact')
                                ->orderBy('date', 'desc')
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
        return view('business.debt-submission.index', ['business' => $business, ]);
    }

    public function store(Business $business, Request $request)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'value' => 'required|numeric',
            'tenor' => 'required|numeric|min:1',
        ]); 

        if (!$request->contact['id']) {
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        $attributes['contact_id'] = $request->contact['id'];
        $attributes['business_id'] = $request->business['id'];
        $attributes['contact_name'] = $request->contact['name'];
        $attributes['author'] = $request->user()->name;

        $debtSubmission = DebtSubmission::create($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    public function show(Business $business, $id)
    {
        $data = DebtSubmission::whereId($id)->with('contact')->first();
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function update(Request $request, Business $business, $id)
    {
        // validasi input
        $attributes = $request->validate([
            'no_ref' => 'required',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'value' => 'required|numeric',
            'status' => 'required|string',
            'tenor' => 'required|numeric|min:1',
        ]); 

        if (!$request->contact['id']) {
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        $attributes['contact_id'] = $request->contact['id'];
        $attributes['business_id'] = $request->business['id'];
        $attributes['contact_name'] = $request->contact['name'];
        $attributes['author'] = $request->user()->name;

        $debtSubmission = DebtSubmission::find($id);
        $debtSubmission->update($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    public function destroy(Business $business, $id)
    {
        $data = DebtSubmission::findOrFail($id);

        $accountReceivable = AccountReceivable::where('debt_submission_id', $data['id'])->first();

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
