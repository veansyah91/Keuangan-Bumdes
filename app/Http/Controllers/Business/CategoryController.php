<?php

namespace App\Http\Controllers\Business;

use App\Models\Product;
use App\Models\Business;
use App\Models\Category;
use Illuminate\Http\Request;

use App\Models\Businessaccount;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        $categories = Category::where('business_id', $business['id'])->orderBy('created_at', 'desc')->paginate(10);
        return view('business.category.index', compact('business', 'categories'));
    }

    public function store(Business $business, Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required'
        ]);

        // cek apakah sudah ada kategori berdasarkan nama 
        $category = Category::where('business_id', $business['id'])->where('nama', $validatedData['nama'])->first();

        if ($category) {
            throw ValidationException::withMessages([
                'message' => ["Nama Kategori Sudah Digunakan"]
            ]);
        }
        
        $category = Category::create([
            'nama' => strtoupper($validatedData['nama']),
            'business_id' => $business['id']
        ]);

        //buat akun
        //persediaan
        $account = Businessaccount::where('business_id', $business['id'])->where('sub_category', 'Persediaan Barang Dagang')->orderBy('id', 'desc')->first();
        $new_code = (int)$account['code'] + 1;
        Businessaccount::create([
            'name' => 'Persediaan ' . $category['nama'],
            'code' => $new_code,
            'is_cash' => false,
            'is_active' => true,
            'sub_classification_account_id' => $account['sub_classification_account_id'],
            'sub_category' => $account['sub_category'],
            'business_id' => $account['business_id'],
        ]);

        //harga pokok penjualan
        $account = Businessaccount::where('business_id', $business['id'])->where('sub_category', 'Harga Pokok Penjualan')->orderBy('id', 'desc')->first();
        $new_code = (int)$account['code'] + 1;
        Businessaccount::create([
            'name' => 'Harga Pokok Penjualan ' . $category['nama'],
            'code' => $new_code,
            'is_cash' => false,
            'is_active' => true,
            'sub_classification_account_id' => $account['sub_classification_account_id'],
            'sub_category' => $account['sub_category'],
            'business_id' => $account['business_id'],
        ]);

        //penjualan
        $account = Businessaccount::where('business_id', $business['id'])->where('sub_category', 'Penjualan Produk')->orderBy('id', 'desc')->first();
        $new_code = (int)$account['code'] + 1;
        Businessaccount::create([
            'name' => 'Penjualan ' . $category['nama'],
            'code' => $new_code,
            'is_cash' => false,
            'is_active' => true,
            'sub_classification_account_id' => $account['sub_classification_account_id'],
            'sub_category' => $account['sub_category'],
            'business_id' => $account['business_id'],
        ]);

        //retur penjualan
        $account = Businessaccount::where('business_id', $business['id'])->where('sub_category', 'Retur Penjualan')->orderBy('id', 'desc')->first();
        $new_code = (int)$account['code'] + 1;
        Businessaccount::create([
            'name' => 'Retur Penjualan ' . $category['nama'],
            'code' => $new_code,
            'is_cash' => false,
            'is_active' => true,
            'sub_classification_account_id' => $account['sub_classification_account_id'],
            'sub_category' => $account['sub_category'],
            'business_id' => $account['business_id'],
        ]);

        //retur pembelian
        $account = Businessaccount::where('business_id', $business['id'])->where('sub_category', 'Retur Pembelian')->orderBy('id', 'desc')->first();
        $new_code = (int)$account['code'] + 1;
        Businessaccount::create([
            'name' => 'Retur Pembelian ' . $category['nama'],
            'code' => $new_code,
            'is_cash' => false,
            'is_active' => true,
            'sub_classification_account_id' => $account['sub_classification_account_id'],
            'sub_category' => $account['sub_category'],
            'business_id' => $account['business_id'],
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $category
        ]);
    }

    public function delete(Business $business, Category $category)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }

        
        $category->delete();

        return redirect('/' . $business['id'] . '/category')->with('Success', 'Berhasil Menghapus Kategori');;
    }

    public function destroy(Business $business, Category $category)
    {
        $product = Product::where('business_id', $business['id'])->where('category', $category['nama'])->first();

        if ($product) {
            throw ValidationException::withMessages([
                'message' => ["Tidak Bisa Dihapus, Kategori Telah Digunakan Pada Produk"]
            ]);
        }
        //delete akun
        $accounts = Businessaccount::where('business_id', $business['id'])->where('name', 'like', '%' . $category['nama'])->get();
        foreach ($accounts as $account) {
            $account->delete();
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'data' => $category
        ]);
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
