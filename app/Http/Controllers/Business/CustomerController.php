<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $customers = Customer::where('business_id', $business['id'])->orderBy('created_at', 'desc')->paginate(10);
        return view('business.customer.index', compact('business', 'customers'));
    }

    public function store(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        // validasi disini 
        $validated = $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'numeric|nullable',
        ]);

        Customer::create([
            'nama' => strtoupper($validated['nama']),
            'alamat' => strtoupper($validated['alamat']),
            'no_hp' => $request->no_hp,
            'business_id' => $business['id']
        ]);

        return redirect('/' . $business['id'] . '/customer')->with('Success', 'Berhasil Menambah Pelanggan');
    }

    public function update(Business $business, Customer $customer, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $validated = $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'numeric|nullable',
        ]);

        $customer->update([
            'nama' => strtoupper($validated['nama']),
            'alamat' => strtoupper($validated['alamat']),
            'no_hp' => $validated['no_hp'],
        ]);

        return redirect('/' . $business['id'] . '/customer')->with('Success', 'Berhasil Mengubah Pelanggan');
    }

    public function delete(Business $business, Customer $customer)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $customer->delete();

        return redirect('/' . $business['id'] . '/customer')->with('Success', 'Berhasil Menghapus Pelanggan');
    }

    public function detail(Business $business, Customer $customer)
    {
        $response = [
            'message' => "Berhasil Mendapatkan Data",
            'status' => "Success",
            'data' => $customer,
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function apiValidate(Request $request)
    {
        // validasi disini 
        $validated = $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'numeric',
        ]);

        $response = [
            'message' => "Data Telah Tervalidasi",
            'status' => 1,
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function search(Business $business, Request $request)
    {
        
        $customer = Customer::where('business_id', $business['id'])
                            ->where('nama', 'like', '%' . $request->search . '%')
                            ->skip(0)->take(5)
                            ->get();

        $response = [
            'message' => "Berhasil Mendapatkan Data Konsumen",
            'status' => 'success',
            'data' => $customer
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }
}
