<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function index(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        } 
        $pemasok = $request['pemasok'] ? $request['pemasok'] : '';
        $kategori = $request['kategori'] ? $request['kategori'] : '';
        $brand = $request['brand'] ? $request['brand'] : '';

        $search = $request['search'] ? $request['search'] : '';

        $products = Product::where('business_id', $business['id'])
                            ->where(function($query) use ($search){
                                $query->where('nama_produk', 'like', '%' . $search . '%')
                                        ->orWhere('kode', 'like', '%' . $search . '%');
                            })                            
                            ->orderBy('nama_produk')->paginate(10)->withQueryString();

        return view('business.product.index', [
            'business' => $business, 
            'pemasok' => $pemasok, 
            'kategori' => $kategori,
            'brand' => $brand, 
            'products' => $products,
            'search' => $search]);
    }

    public function create(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        } 
        $pemasok = $request['pemasok'] ? $request['pemasok'] : '';
        $kategori = $request['kategori'] ? $request['kategori'] : '';
        $brand = $request['brand'] ? $request['brand'] : '';

        $products = Product::where('business_id', $business['id'])->whereDate('created_at', Carbon::today()->toDateString())->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('business.product.create', 
                    ([
                        'business' => $business, 
                        'pemasok' => $pemasok, 
                        'kategori' => $kategori,
                        'brand' => $brand, 
                        'products' => $products]));
    }

    public function store(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        } 
        $create = Product::create([
            'pemasok' => strtoupper($request->pemasok),
            'brand' => strtoupper($request->brand),
            'kategori' => strtoupper($request->kategori),
            'kode' => $request->kode,
            'nama_produk' => strtoupper($request->nama),
            'modal' => $request->modal ? $request->modal : 0,
            'jual' => $request->jual,
            'business_id' => $business['id'],
        ]);

        if ($request->page == 'create') {
            return redirect('/' . $business['id'] . '/product/create?pemasok=' . $request->pemasok . '&kategori=' . $request->kategori . '&brand=' . $request->brand)->with('Success', 'Berhasil Menambah Produk');
        }
        return redirect('/' . $business['id'] . '/product')->with('Success', 'Berhasil Menambah Produk');
    }

    public function update(Business $business, Product $product, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        } 
        $product->update([
            'pemasok' => strtoupper($request->pemasok),
            'brand' => strtoupper($request->brand),
            'kategori' => strtoupper($request->kategori),
            'kode' => $request->kode,
            'nama_produk' => strtoupper($request->nama),
            'modal' => $request->modal ? $request->modal : 0,
            'jual' => $request->jual,

        ]);
        if ($request->page == 'create') {
            return redirect('/' . $business['id'] . '/product/create?pemasok=' . $request->pemasok . '&kategori=' . $request->kategori . '&brand=' . $request->brand)->with('Success', 'Berhasil Mengubah Produk');
        }
        return redirect('/' . $business['id'] . '/product')->with('Success', 'Berhasil Mengubah Produk');
    }

    public function delete(Business $business, Product $product, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        } 
        $product->delete();

        if ($request->page == 'create') {
            return redirect('/' . $business['id'] . '/product/create?pemasok=' . $request->pemasok . '&kategori=' . $request->kategori . '&brand=' . $request->brand)->with('Success', 'Berhasil Menghapus Produk');
        }
        return redirect('/' . $business['id'] . '/product')->with('Success', 'Berhasil Menghapus Produk');
    }

    public function detail(Business $business, Product $product)
    {
        
        $response = [
            'message' => "Data Telah Tervalidasi",
            'status' => 'Success',
            'data' => $product
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function search(Business $business, Request $request)
    {
        if ($business['kategori'] == 'Retail') {
            $product = Product::where('business_id', $business['id'])
                            ->where(function($query) use ($request) {
                                $query->where('kode', 'like', '%' . $request->search . '%')
                                      ->orWhere('nama_produk', 'like', '%' . $request->search . '%');
                            })
                            ->whereHas('stock', function($query) {
                                $query->where('jumlah', '>', '0');
                            })
                            ->skip(0)->take(5)
                            ->get();
        }

        if ($business['kategori'] == 'Restoran' || $business['kategori'] == 'Lainnya') {
            $product = Product::where('business_id', $business['id'])
                            ->where(function($query) use ($request) {
                                $query->where('kode', 'like', '%' . $request->search . '%')
                                      ->orWhere('nama_produk', 'like', '%' . $request->search . '%');
                            })
                            ->skip(0)->take(5)
                            ->get();
        }
        $response = [
            'message' => "Data Telah Tervalidasi",
            'status' => 'Success',
            'data' => $product
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function searchMenu(Business $business, Request $request)
    {
        $product = Product::where('business_id', $business['id'])
                            ->where(function($query) use ($request) {
                                $query->where('kode', 'like', '%' . $request->search . '%')
                                      ->orWhere('nama_produk', 'like', '%' . $request->search . '%');
                            })
                            ->skip(0)->take(5)
                            ->get();

        $response = [
            'message' => "Data Telah Tervalidasi",
            'status' => 'Success',
            'data' => $product
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
            'kategori' => 'required',
            'jual' => 'required',
            'kode' => 'required',
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
