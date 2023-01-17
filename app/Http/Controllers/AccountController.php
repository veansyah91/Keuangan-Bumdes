<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use App\Models\SubClassificationAccount;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.account.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
    public function store(Request $request)
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

        $account = Account::create($attributes);
        

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'status' => 'success',
            'data' => Account::find($id),
        ]); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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

        $account = Account::find($id)->update($attributes);

        return response()->json([
            'status' => 'success',
            'data' => Account::find($id),
        ]);    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getApiData(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => Account::filter(request(['search']))->isCash(request(['is_cash']))->orderBy('code', 'asc')->paginate(100),
        ]);
    }
}
