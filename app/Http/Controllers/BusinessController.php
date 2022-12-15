<?php

namespace App\Http\Controllers;

use App\Models\Account;
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

        // buat akun 
        //kategori asset
        $account = Account::where('name', 'like', 'Harta Unit Usaha%')->first();

        if ($account) {
            //create account based on business name
            Account::create([
                'name' => 'Harta Unit Usaha ' . $validatedData['nama'],
                'code' => (string)((int)$account['code'] + 1),
                'is_cash' => false,
                'is_active' => true,
                'sub_classification_account_id' => $account['sub_classification_account_id '],
                'sub_category' => $account['sub_category '],
            ]);
        }

        //kategori modal
        $account = Account::where('name', 'like', 'Modal Unit Usaha%')->first();
        if ($account) {
            //create account based on business name
            Account::create([
                'name' => 'Modal Unit Usaha ' . $validatedData['nama'],
                'code' => (string)((int)$account['code'] + 1),
                'is_cash' => false,
                'is_active' => true,
                'sub_classification_account_id' => $account['sub_classification_account_id '],
                'sub_category' => $account['sub_category '],
            ]);
        }
        
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
