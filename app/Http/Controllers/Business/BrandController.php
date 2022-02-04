<?php

namespace App\Http\Controllers\Business;

use App\Models\Brand;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
    public function index(Business $business)
    {
        $brands = Brand::where('business_id', $business['id'])->orderBy('created_at', 'desc')->paginate(10);
        return view('business.brand.index', compact('business', 'brands'));
    }

    public function store(Business $business, Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required'
        ]);
        
        Brand::create([
            'nama' => strtoupper($validatedData['nama']),
            'business_id' => $business['id']
        ]);

        return redirect('/' . $business['id'] . '/brand')->with('Success', 'Berhasil Menambah Brand');
    }

    public function delete(Business $business, Brand $brand)
    {
        $brand->delete();

        return redirect('/' . $business['id'] . '/brand')->with('Success', 'Berhasil Menghapus Brand');
    }

    public function search(Business $business, Request $request)
    {
        $brands = Brand::where('business_id', $business['id'])->where('nama', 'like', '%' . $request->search . '%')->get();

        $response = [
            'message' => "Berhasil Mendapatkan Data",
            'status' => "Success",
            'data' => $brands,
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
