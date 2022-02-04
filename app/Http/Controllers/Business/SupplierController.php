<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Symfony\Component\HttpFoundation\Response;

class SupplierController extends Controller
{
    public function index(Business $business)
    {
        $suppliers = Supplier::where('business_id', $business['id'])->orderBy('created_at', 'desc')->paginate(10);
        return view('business.supplier.index', compact('business', 'suppliers'));
    }

    public function store(Business $business, Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'numeric|nullable',
        ]);

        Supplier::create([
            'nama' => strtoupper($validated['nama']),
            'alamat' => strtoupper($validated['alamat']),
            'no_hp' => $request->no_hp,
            'business_id' => $business['id']
        ]);

        return redirect('/' . $business['id'] . '/supplier')->with('Success', 'Berhasil Menambah Pemasok');
    }

    public function update(Business $business, Supplier $supplier, Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'numeric|nullable',
        ]);

        $supplier->update([
            'nama' => strtoupper($validated['nama']),
            'alamat' => strtoupper($validated['alamat']),
            'no_hp' => $validated['no_hp'],
        ]);

        return redirect('/' . $business['id'] . '/supplier')->with('Success', 'Berhasil Mengubah Pemasok');
    }

    public function delete(Business $business, Supplier $supplier)
    {
        $supplier->delete();

        return redirect('/' . $business['id'] . '/supplier')->with('Success', 'Berhasil Menghapus Pemasok');
    }

    public function search(Business $business, Request $request)
    {
        $suppliers = Supplier::where('business_id', $business['id'])->where('nama', 'like', '%' . $request->search . '%')->get();

        $response = [
            'message' => "Berhasil Mendapatkan Data",
            'status' => "Success",
            'data' => $suppliers,
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function detail(Business $business, Supplier $supplier)
    {
        $response = [
            'message' => "Berhasil Mendapatkan Data",
            'status' => "Success",
            'data' => $supplier,
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
}
