<?php

namespace App\Http\Controllers\Business;

use App\Models\Asset;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AssetController extends Controller
{
    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $assets = Asset::orderBy('created_at', 'desc')
                        ->orderBy('jumlah_bagus')
                        ->where('business_id', $business['id'])
                        ->paginate(10)
                        ->withQueryString();

        $getAsset = Asset::where('business_id', $business['id'])->get();
        $sumAsset = $getAsset->sum(function ($query){
            return $query['harga_satuan'] * $query['jumlah_bagus'];
        });
        return view('business.asset.index', compact('business', 'assets', 'sumAsset'));
    }

    public function store(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $create = Asset::create([
            'name_item' => $request->nama,
            'harga_satuan' => $request->harga,
            'kode' => $request->kode,
            'jumlah_bagus' => $request->jumlah_bagus,
            'jumlah_rusak' => $request->jumlah_rusak,
            'tanggal_masuk' => $request->tanggal_masuk,
            'business_id' => $business['id'],
        ]);

        return redirect('/' . $business['id'] . '/asset')->with('Success', 'Berhasil Menambah Aset');
    }

    public function update(Business $business, Asset $asset, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $asset->update([
            'name_item' => $request->nama,
            'harga_satuan' => $request->harga,
            'kode' => $request->kode,
            'jumlah_bagus' => $request->jumlah_bagus,
            'jumlah_rusak' => $request->jumlah_rusak,
            'tanggal_masuk' => $request->tanggal_masuk,
        ]);
        return redirect('/' . $business['id'] . '/asset')->with('Success', 'Berhasil Mengubah Aset');
    }

    public function delete(Business $business, Asset $asset)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser) {
            return abort(403);
        } 
        $asset->delete();
        return redirect('/' . $business['id'] . '/asset')->with('Success', 'Berhasil Menghapus Aset');
    }

    public function detail(Asset $asset)
    {
        $response = [
            'message' => "Data Telah Tervalidasi",
            'status' => 'Success',
            'data' => $asset,
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function apiValidate(Business $business, Request $request)
    {
        // validasi disini 
        $validated = $request->validate([
            'nama_item' => 'required',
            'harga_satuan' => 'required',
            'jumlah_bagus' => 'required',
            'jumlah_rusak' => 'required',
            'tanggal_masuk' => 'required',
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
