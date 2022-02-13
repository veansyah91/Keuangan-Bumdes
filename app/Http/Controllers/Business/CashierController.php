<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Stock;
use App\Models\Invoice;
use App\Models\Business;
use App\Models\Customer;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\InvoiceProduct;
use App\Models\AccountReceivable;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CashierController extends Controller
{
    public function index(Business $business)
    {
        $invoice = Invoice::where('business_id', $business['id'])->get()->last();
        $identity = Identity::first();
        
        return view('business.cashier.index', [
            'business' => $business,
            'invoice' => $invoice ? $invoice['nomor'] + 1 : 1,
            'identity' => $identity
        ]);
    }

    public function store(Business $business, Request $request)
    {
        // cari costomer berdasarkan nama
        $customer = Customer::where('business_id', $business['id'])->where('nama', $request->namaPelanggan)->first();

        // buat invoice 
        $invoice = Invoice::create([
            'nomor' => $request->nomorNota,
            'jumlah' => $request->sisa >= 0 ? $request->total : $request->total + $request->sisa,
            'business_id' => $business['id'],
            'nama_konsumen' => strtoupper($request->namaPelanggan),
            'customer_id' => $customer ? $customer['id'] : null,
            'operator' => $request->operator
        ]);

        // jika sisa -0 maka masukkan ke piutang
        if ($request->sisa < 0) {
            $accountReceivables = AccountReceivable::create([
                'sisa' => $request->sisa * -1,
                'nomor_nota' => $request->nomorNota,
                'nama_konsumen' => strtoupper($request->namaPelanggan),
                'customer_id' => $customer ? $customer['id'] : null,
                'business_id' => $business['id'],
                'invoice_id' => $invoice['id']
            ]);
        }
        
        foreach ($request->products as $product) {
            DB::table('invoice_product')->insert([
                'jumlah' => $product['jumlah'],
                'harga' => $product['harga'],
                'product_id' => $product['idProduk'],
                'invoice_id' => $invoice['id'],
            ]);

            if ($business['kategori'] == 'Retail') {
                // kurangi stok 
                $stockSekarang = Stock::where('product_id',$product['idProduk'])->first();
                $stock = $stockSekarang['jumlah'] - $product['jumlah'];

                $stockSekarang->update([
                    'jumlah' => $stock
                ]);
            }

        }

        $response = [
            'message' => "Berhasil Mengirimkan Data",
            'status' => "Success",
            
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function invoiceDetail(Invoice $invoice)
    {
        
        $response = [
            'message' => "Berhasil Mengirimkan Data",
            'status' => "Success",
            'data' => $invoice->products
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function indexRestaurant(Business $business)
    {
        $identity = Identity::first();
        $invoice = Invoice::where('business_id', $business['id'])->get()->last();

        return view('business.cashier.index-restaurant', [
            'business' => $business,
            'invoice' => $invoice ? $invoice['nomor'] + 1 : 1,
            'identity' => $identity
        ]);
    }

    public function deleteInvoiceDetail($invoiceId, $productId)
    {
        // kurangi piutang

        $accountReceivable = AccountReceivable::where('invoice_id', $invoiceId)->first();
        $old = $accountReceivable['sisa'];

        $data = DB::table('invoice_product')->where('invoice_id', $invoiceId)->where('product_id', $productId)->first();

        $accountReceivable->update([
            'sisa' => $old - $data->harga * $data->jumlah
        ]);

        $data = DB::table('invoice_product')->where('invoice_id', $invoiceId)->where('product_id', $productId)->delete();

        $response = [
            'message' => "Berhasil Menghapus Data",
            'status' => 200,
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function addOrder(Request $request)
    {
        // cari apakah sudah order 
        $piutang = DB::table('invoice_product')->where('invoice_id', $request->invoiceId)->where('product_id', $request->productId)->first();

        if ($piutang) {
            DB::table('invoice_product')->where('invoice_id', $request->invoiceId)->where('product_id', $request->productId)->update([
                'jumlah' => $request->jumlah + $piutang->jumlah,
            ]);
        } else {
            DB::table('invoice_product')->insert([
                'jumlah' => $request->jumlah,
                'harga' => $request->harga,
                'product_id' => $request->productId,
                'invoice_id' => $request->invoiceId,
            ]);
        }
        
        $accountReceivable = AccountReceivable::find($request->id);
        $old = $accountReceivable['sisa'];

        $accountReceivable->update([
            'sisa' => $old + $request->jumlah * $request->harga
        ]);

        $response = [
            'message' => "Berhasil Menambah Data",
            'status' => 200,
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }

    public function invoiceUpdate(Invoice $invoice, Request $request)
    {
        $old = $invoice['jumlah'];

        $invoice->update([
            'jumlah' => $old + $request['total']
        ]);

        $accountReceivable = AccountReceivable::where('invoice_id', $invoice['id'])->first();
        $old = $accountReceivable['sisa'];
        $accountReceivable->update([
            'sisa' => $old - $request['total']
        ]);

        $response = [
            'message' => "Berhasil Menghapus Data",
            'status' => 200,
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }
}
