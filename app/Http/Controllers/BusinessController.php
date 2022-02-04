<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index()
    {
        $businesses = Business::orderBy('created_at', 'desc')->get();

        return view('admin.business.index', [
            'businesses' => $businesses 
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required'
        ]);

        Business::create([
            'nama' => $validatedData['nama'],
            'kategori' => $request->kategori,
            'status' => 'active',
        ]);

        return redirect('/business');
    }

    public function show(Business $business)
    {
        //
    }
    public function edit(Business $business)
    {
        //
    }

    public function update(Request $request, Business $business)
    {
        $validatedData = $request->validate([
            'nama' => 'required'
        ]);

        $business->update([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'status' => $request->status,
        ]);

        return redirect('/business');
    }

    public function destroy(Business $business)
    {
        //
    }
}
