<?php

namespace App\Http\Controllers\Business;

use App\Models\Stock;
use App\Models\Product;
use App\Models\Business;
use App\Models\Incomingitem;
use Illuminate\Http\Request;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IncomingItemController extends Controller
{
    public function index(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $incomingItems = Incomingitem::where('business_id', $business['id'])->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        return view('business.incoming-item.index', compact('business','request','incomingItems'));
    }

    public function create(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $pemasok = $request['pemasok'] ?? $request['pemasok'];
        $kategori = $request['kategori'] ?? $request['kategori'];
        $brand = $request['brand'] ?? $request['brand'];

        $incomingItem = $request['incomingItemId'] ? IncomingItem::find($request['incomingItemId']) : false; 

        $stocks = Stock::where('incomingitem_id', $incomingItem['id'])->get();

        return view('business.incoming-item.create', compact('business','request', 'pemasok', 'kategori', 'brand', 'incomingItem', 'stocks'));
    }

    public function store(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        // tambah data Incoming Item
        // cek apakah nota sudah pernah dibuat            

        $incomingItem = IncomingItem::updateOrCreate(
            [
                'nomor_nota' => $request->nomor,
                'business_id' => $business['id'],
            ],
            [
                'tanggal_nota' => $request->tanggal_nota,
                'tanggal_masuk' => $request->tanggal_masuk,
            ]
                
        );

        $product = Product::updateOrCreate(
            [
                'kode' => $request->kode,
                'business_id' => $business['id'],
            ], 
            [
                'pemasok' => strtoupper($request->pemasok),
                'brand' => strtoupper($request->brand),
                'kategori' => strtoupper($request->kategori),
                'kode' => $request->kode,
                'nama_produk' => strtoupper($request->nama),
                'modal' => $request->modal ? : 0,
                'jual' => $request->jual,
            ]
        );

        $stock = Stock::create([
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
            'product_id' => $product['id'],
            'incomingitem_id' => $incomingItem['id'],
        ]);

        $jumlahSemua = $incomingItem['jumlah'];
        $jumlahSemua += $request->modal * $request->jumlah;

        $incomingItem->update([
            'jumlah' => $jumlahSemua
        ]);
        return redirect('/' . $business['id'] . '/incoming-item/create?incomingItemId=' . $incomingItem['id'] . '&pemasok=' . $request->pemasok . '&kategori=' . $request->kategori . '&brand=' . $request->brand)->with('Success', 'Berhasil Menambah Produk');;
        
    }

    public function update(Business $business, Stock $stock, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 

        $incomingItem = Incomingitem::find($stock['incomingitem_id']);
        $product = Product::find($stock['product_id']);

        $totalJumlah = $incomingItem['jumlah'] - $stock['jumlah'] * $product['modal'];
        $totalJumlah += $request->jumlah * $request->modal;

        $incomingItem->update([
            'nomor_nota' => $request->nomor,
            'tanggal_nota' => $request->tanggal_nota,
            'tanggal_masuk' => $request->tanggal_masuk,
            'jumlah' => $totalJumlah 
        ]);

        $product->update([
            'kode' => $request->kode,
            'pemasok' => strtoupper($request->pemasok),
            'brand' => strtoupper($request->brand),
            'kategori' => strtoupper($request->kategori),
            'kode' => $request->kode,
            'nama_produk' => strtoupper($request->nama),
            'modal' => $request->modal ? : 0,
            'jual' => $request->jual,
        ]);

        $stock->update([
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
        ]);

        return redirect('/' . $business['id'] . '/incoming-item/create?incomingItemId=' . $incomingItem['id'])->with('Success', 'Berhasil Mengubah Produk');
    }

    public function apiValidate(Request $request)
    {
        // validasi disini 
        $validated = $request->validate([
            'nama' => 'required',
            'kategori' => 'required',
            'jual' => 'required',
            'kode' => 'required',
            'modal' => 'required',
            'nomor' => 'required',
            'tanggalNota' => 'required',
            'tanggalMasuk' => 'required',
            'brand' => 'required',
            'pemasok' => 'required',
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

    public function deleteStock(Business $business, Stock $stock)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $product = Product::find($stock['product_id']);

        $incomingItem = IncomingItem::find($stock['incomingitem_id']);
        // kurangi jumlah di table barang masuk
        $jumlahSekarang = $incomingItem['jumlah'] - $product['modal'] * $stock['jumlah'];
        $incomingItem->update([
            'jumlah' => $jumlahSekarang
        ]);
        $product->delete();
        $stock->delete();

        return redirect('/' . $business['id'] . '/incoming-item/create?incomingItemId=' . $incomingItem['id'])->with('Success', 'Berhasil Menghapus');
    }

    public function getStock(Stock $stock)
    {
        $incomingItem = Incomingitem::find($stock['incomingitem_id']);
        $product = Product::find($stock['product_id']);
        $response = [
            'message' => "Data Telah Tervalidasi",
            'status' => 'Succcess',
            'data' => [
                        'stock' => $stock,
                        'product' => $product,
                        'incomingItem' => $incomingItem,
                      ]
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }
}
