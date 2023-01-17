<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\Businessaccount;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\SubClassificationAccount;

class BusinessAccountController extends Controller
{
    public function getApiData(Business $business)
    {
        return response()->json([
            'status' => 'success',
            'data' => Businessaccount::filter(request(['search']))
                                        ->payment(request(['payment']))
                                        ->payable(request(['payable']))
                                        ->isCash(request(['is_cash']))
                                        ->where('business_id', $business['id'])
                                        ->orderBy('code', 'asc')
                                        ->paginate(100),
        ]);
    }

    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.account.index', compact('business'));
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Business $business)
    {
        $attributes = $request->validate([
            'name' => 'required',
            'code' => 'required',
            'sub_category' => 'required',
            'is_cash' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $sub = SubClassificationAccount::where('name', $request->sub_category)->first();
        

        $attributes['business_id'] = $business['id'];
        $attributes['sub_classification_account_id'] = $sub['id'];

        $account = Businessaccount::create($attributes);
        
        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    
    public function show(Business $business, Businessaccount $businessaccount)
    {
        return response()->json([
            'status' => 'success',
            'data' => $businessaccount,
        ]); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Businessaccount  $businessaccount
     * @return \Illuminate\Http\Response
     */
    public function edit(Businessaccount $businessaccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Businessaccount  $businessaccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Business $business, Businessaccount $businessaccount)

    {
        $attributes = $request->validate([
            'name' => 'required',
            'code' => 'required',
            'sub_category' => 'required',
            'is_cash' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $sub = SubClassificationAccount::where('name', $request->sub_category)->first();

        $attributes['sub_classification_account_id'] = $sub['id'];

        $businessaccount->update($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $businessaccount,
        ]);    
    }
    
    public function destroy(Businessaccount $businessaccount)
    {
        //
    }
}
