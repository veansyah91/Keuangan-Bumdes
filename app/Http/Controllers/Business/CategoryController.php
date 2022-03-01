<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Helpers\BusinessUserHelper;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $categories = Category::where('business_id', $business['id'])->orderBy('created_at', 'desc')->paginate(10);
        return view('business.category.index', compact('business', 'categories'));
    }

    public function store(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $validatedData = $request->validate([
            'nama' => 'required'
        ]);
        
        Category::create([
            'nama' => strtoupper($validatedData['nama']),
            'business_id' => $business['id']
        ]);

        return redirect('/' . $business['id'] . '/category')->with('Success', 'Berhasil Menambah Kategori');;
    }

    public function delete(Business $business, Category $category)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $category->delete();

        return redirect('/' . $business['id'] . '/category')->with('Success', 'Berhasil Menghapus Kategori');;
    }

    public function search(Business $business, Request $request)
    {
        $categories = Category::where('business_id', $business['id'])->where('nama', 'like', '%' . $request->search . '%')->get();

        $response = [
            'message' => "Berhasil Mendapatkan Data",
            'status' => "Success",
            'data' => $categories,
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
