<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Helpers\BusinessUserHelper;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StockController extends Controller
{
    public function index(Business $business, Request $request)
    {   
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $search = $request['search'];
        
        $stocks = Stock::whereHas('product', function($query) use ($search, $business){
                        $query->where('nama_produk', 'like', '%' . $search . '%')
                              ->orWhere('kode', 'like', '%' . $search . '%');
                        $query->where('business_id', $business['id']);
                        $query->orderBy('kategori', 'asc');
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->withQueryString();

        return view('business.stock.index', compact('business','request','stocks'));
    }

    public function create(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $pemasok = $request['pemasok'] ? $request['pemasok'] : '';
        $kategori = $request['kategori'] ? $request['kategori'] : '';
        $brand = $request['brand'] ? $request['brand'] : '';

        $products = Product::where('business_id', $business['id'])->whereDate('created_at', Carbon::today()->toDateString())->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('business.stock.create', 
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
        
        if (!$businessUser) {
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

        $createStock = Stock::create([
            'satuan' => $request->satuan,
            'jumlah' => $request->jumlah,
            'product_id' => $create['id']
        ]);

        if ($request->page == 'create') {
            return redirect('/' . $business['id'] . '/stock/create?pemasok=' . $request->pemasok . '&kategori=' . $request->kategori . '&brand=' . $request->brand)->with('Success', 'Berhasil Menambahkan Produk');
        }
        return redirect('/' . $business['id'] . '/stock')->with('Success', 'Berhasil Menambahkan Produk');
    }

    public function update(Business $business, Stock $stock, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $stock->update([
            'satuan' => $request->satuan,
            'jumlah' => $request->jumlah,
        ]);

        $product = Product::find($stock['product_id'])->update([
            'pemasok' => strtoupper($request->pemasok),
            'brand' => strtoupper($request->brand),
            'kategori' => strtoupper($request->kategori),
            'kode' => $request->kode,
            'nama_produk' => strtoupper($request->nama),
            'modal' => $request->modal ? $request->modal : 0,
            'jual' => $request->jual,
        ]);

        if ($request->page == 'create') {
            return redirect('/' . $business['id'] . '/stock/create?pemasok=' . $request->pemasok . '&kategori=' . $request->kategori . '&brand=' . $request->brand)->with('Success', 'Berhasil Mengubah Produk');
        }
        return redirect('/' . $business['id'] . '/stock')->with('Success', 'Berhasil Mengubah Produk');
    }

    public function delete(Business $business, Stock $stock, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $product = Product::find($stock['product_id'])->delete();

        if ($request->page == 'create') {
            return redirect('/' . $business['id'] . '/stock/create?pemasok=' . $request->pemasok . '&kategori=' . $request->kategori . '&brand=' . $request->brand)->with('Success', 'Berhasil Menghapus Produk');
        }
        return redirect('/' . $business['id'] . '/stock')->with('Success', 'Berhasil Menghapus Produk');
    }

    public function detail(Business $business, Stock $stock)
    {
        
        $response = [
            'message' => "Data Telah Tervalidasi",
            'status' => 'Success',
            'data' => [
                'stock' => $stock,
                'product' => Product::find($stock['product_id'])
            ]
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
            // 'modal' => 'required',
            // 'brand' => 'required',
            // 'pemasok' => 'required',
            'jumlah' => 'required',
            'satuan' => 'required|in:pcs, gram, kg, m',
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

    public function search($product)
    {
        $data = Stock::where('product_id', $product)->first();

        $response = [
            'message' => "Data Telah Tervalidasi",
            'status' => $data ? 200 : 402,
            'data' => $data
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }
}
