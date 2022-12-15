<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use App\Models\SubClassificationAccount;
use Illuminate\Validation\ValidationException;

class SubCategoryAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => SubClassificationAccount::filter(request(['search', 'limit']))->orderBy('code', 'asc')->get()
        ]);
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
        // return response()->json([
        //     'status' => 'success',
        //     'data' => $request
        // ]);
        $attributes = $request->validate([
            'name' => 'required',
            'code' => 'required'
        ]);

        //cek apakah sudah ada kode yang sama
        $subs = SubClassificationAccount::where('code', $attributes['code'])->first();

        if ($subs) {
            throw ValidationException::withMessages([
                'code' => ["Kode Telah Digunakan"]
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => SubClassificationAccount::create($attributes),
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sub = SubClassificationAccount::find($id);

        //cek apakah telah digunakan di akun
        $account = Account::where('sub_category', $sub['name'])->first();

        if ($account) {
            throw ValidationException::withMessages([
                'message' => ["Tidak Bisa Dihapus, Sub Klasifikasi Telah Digunakan Pada Akun"]
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $sub->delete(),
        ]);
    }
}
