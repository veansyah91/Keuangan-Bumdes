<?php

namespace App\Http\Controllers\Business;

use App\Models\Stock;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Business;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Models\Businessjournal;
use App\Models\Businesscashflow;
use App\Models\AccountReceivable;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function newNoRefInvoice($no_ref_request, $no_ref_invoice){
        $split_invoice_ref_no = explode("-", $no_ref_invoice);
        $old_ref_no = (int)$split_invoice_ref_no[1];
        $new_ref_no = 1000000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }

    public function noRefInvoiceRecomendation(Business $business){
        $fix_ref_no = '';
        $date = date('Ymd');

        $endInvoice = Invoice::where('business_id', $business['id'])
                            ->where('no_ref', 'like', 'INV-' . $date . '%')
                            ->orderBy('id', 'desc')
                            ->first();

        $newInvoice = 'INV-' . $date . '0001';

        if ($endInvoice) {
            $split_end_invoice = explode('-', $endInvoice['no_ref']);

            $newNumber = (int)$split_end_invoice[1] + 1;

            $newInvoice = 'INV-' . $newNumber;
        }

        return response()->json([
            'status' => 'success',
            'data' => $newInvoice,
        ]);
    }

    public function getData(Business $business){
        return response()->json([
            'status' => 'success',
            'data' => Invoice::filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->where('business_id', $business['id'])->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50)->withQueryString()
        ]);
    }

    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.invoice.index', [
            'business' => $business, ]);
    }

    public function create(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.invoice.create', [
            'business' => $business, ]);
    }

    public function edit(Business $business, $id)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.invoice.edit', [
            'business' => $business,
            'invoice' => $id
        ]);
    }

    public function store(Business $business, Request $request)
    {
        // validasi input inventory adjustment 
        $attributes = $request->validate([
            'no_ref' => 'string|required',
            'date' => 'date|required',
            'description' => 'string|nullable',
        ]);
        $attributes['author'] = $request->user()->name;
        $attributes['business_id'] = $business['id'];
        $attributes['contact_id'] = $request->contact['id'];
        $attributes['contact_name'] = $request->contact['name'];
        $attributes['date'] = $request->date;
        $attributes['value'] = $request->total;

        //buat data pada invoices
        $invoice = Invoice::create($attributes);

        //buat data pada invoice_product
        foreach ($request->listInput as $listInput) {
            $product = Product::find($listInput['productId']);
            $invoice->products()->save($product, ['qty' => $listInput['qty'], 'value' => $listInput['total']]);
        }

        foreach ($request->listInput as $listInput) {
            
            //akun Penjualan Berdasarkan Kategori (credit)
                $product = Product::find($listInput['productId']);
                $account_name = 'Penjualan ' . $product['category'];
                $account = Businessaccount::where('business_id', $business['id'])           
                                            ->where('name', $account_name)
                                            ->first();

                Businessledger::create([
                    'account_name' => $account['name'],
                    'account_id' => $account['id'],
                    'debit' => 0,
                    'credit' => $listInput['total'],
                    'no_ref' => $attributes['no_ref'],
                    'date' => $attributes['date'],
                    'description' => $attributes['description'],
                    'account_code' => $account['code'],
                    'author' => $request->user()->name,
                    'business_id' => $business['id']
                ]);
            //

            //lakukan loop berdasarkan list
                if ($product['is_stock_checked']) {
                    //apabila pada produk dilakukan cek stok maka lakukan transaksi dibawah:
                    //tambah kan data pada table stok dimana nilai produk(total) pada kredit, dan qty bernilai negatif karena terjadi pengurangan jumlah stok
                    //untuk HPP dilakukan metode average, yakni 
                    //1. cari tabel stok dimana qty > 1, misalkan nama variabel $stocks
                    $stocks = Stock::where('product_id', $product['id'])
                                    ->where('qty', '>', 0)
                                    ->get();

                    //2. lalu cari hpp dengan rumus $stocks->sum('debit')/$stocks->sum('qty)
                    $cogs = $stocks->sum('debit') / $stocks->sum('qty');
                    $valueProducts = $cogs * $listInput['qty'];

                    //simpan hpp pada variabel lain untuk digunakan pada input buku besar
                    Stock::create([
                        'qty' => -$listInput['qty'],
                        'unit' => $product['unit'],
                        'product_id' => $product['id'],
                        'debit' => 0,
                        'credit' => $valueProducts,
                        'no_ref' => $attributes['no_ref'],
                        'date' => $attributes['date'],
                        'business_id' => $business['id'],
                        'contact' => $attributes['contact_name'] 
                    ]);
                    
                    //Buku besar
                    //debit
                    //HPP nama akun berdasarkan kategori produk
                    $account_name = 'Harga Pokok Penjualan ' . $product['category'];
                    $account = Businessaccount::where('business_id', $business['id'])           
                                            ->where('name', $account_name)
                                            ->first();
                    Businessledger::create([
                        'account_name' => $account['name'],
                        'account_id' => $account['id'],
                        'debit' => $valueProducts,
                        'credit' => 0,
                        'no_ref' => $attributes['no_ref'],
                        'date' => $attributes['date'],
                        'description' => $attributes['description'],
                        'account_code' => $account['code'],
                        'author' => $request->user()->name,
                        'business_id' => $business['id']
                    ]);

                    //credit
                    //Persediaan nama akun berdasarkan karegori produk
                    $account_name = 'Persediaan ' . $product['category'];
                    $account = Businessaccount::where('business_id', $business['id'])           
                                            ->where('name', $account_name)
                                            ->first();
                    Businessledger::create([
                        'account_name' => $account['name'],
                        'account_id' => $account['id'],
                        'debit' => 0,
                        'credit' => $valueProducts,
                        'no_ref' => $attributes['no_ref'],
                        'date' => $attributes['date'],
                        'description' => $attributes['description'],
                        'account_code' => $account['code'],
                        'author' => $request->user()->name,
                        'business_id' => $business['id']
                    ]);
                }
            //
        }

        //(debit)
        //akun kas dan setara kas jika debit.value > 0 
        if ($request->debit['value'] > 0) {
            $account = Businessaccount::find($request->debit['account_id']);
            Businessledger::create([
                'account_name' => $account['name'],
                'account_id' => $account['id'],
                'debit' => $request->debit['value'],
                'credit' => 0,
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'description' => $attributes['description'],
                'account_code' => $account['code'],
                'author' => $request->user()->name,
                'business_id' => $business['id']
            ]);

            $account_name = 'Penjualan Produk';
            $account = Businessaccount::where('business_id', $business['id'])           
                                        ->where('name', $account_name)
                                        ->first();
            //tambahkan data pada tabel arus kas pada debit (operation)
            Businesscashflow::create([
                'account_id' => $account['id'],
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'account_code' => $account['code'],
                'account_name' => $account['name'],
                'type' => 'operation',
                'debit' => $request->debit['value'],
                'credit' => 0,
                'business_id' => $business['id']
            ]);
        }

        //akun piutang datang jika balance <= 0
        if ($request->balance < 0) {
            $account = Businessaccount::where('business_id', $business['id'])           
                                        ->where('name', 'Piutang Dagang')
                                        ->first();

            Businessledger::create([
                'account_name' => $account['name'],
                'account_id' => $account['id'],
                'debit' => -$request->balance,
                'credit' => 0,
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'description' => $attributes['description'],
                'account_code' => $account['code'],
                'author' => $request->user()->name,
                'business_id' => $business['id']
            ]);

            //masukkan data ke table account receivable
                AccountReceivable::create([
                    'no_ref' => $attributes['no_ref'],
                    'business_id' => $business['id'],
                    'invoice_id' => $invoice['id'],
                    'contact_id' => $attributes['contact_id'],
                    'contact_name' => $attributes['contact_name'],
                    'debit' => -$request->balance,
                    'credit' => 0,
                    'date' => $attributes['date'],
                    'description' => $attributes['description'],
                ]);

            // 
        }

        //masukkan data ke journal
        //untuk nilai jurnal, ambul akumulatif dari nilai buku besar bedasarkan salah satu debit atau credit, dengan mengacu pada no_ref
        $ledgerValue = Businessledger::where('business_id', $business['id'])->where('no_ref', $attributes['no_ref'])->get()->sum('debit');
        
        $journal = Businessjournal::create([
            'no_ref' => $attributes['no_ref'],
            'desc' => $attributes['description'],
            'value' => $ledgerValue,
            'detail' => 'Transaksi Penjualan',
            'date' => $attributes['date'],
            'author' =>   $request->user()->name,
            'source' => "Dari Faktur Penjualan",
            'business_id' => $business['id']
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $invoice,
        ]);
    }

    public function update(Business $business, $id, Request $request)
    {
        // validasi input inventory adjustment 
        $attributes = $request->validate([
            'no_ref' => 'string|required',
            'date' => 'date|required',
            'description' => 'string|nullable',
        ]);

        $attributes['author'] = $request->user()->name;
        $attributes['business_id'] = $business['id'];
        $attributes['contact_id'] = $request->contact['id'];
        $attributes['contact_name'] = $request->contact['name'];
        $attributes['date'] = $request->date;
        $attributes['value'] = $request->total;

        $invoice = Invoice::find($id);

        //hapus data pada table buku besar
            $ledgers = Businessledger::where('business_id', $business['id'])
                                        ->where('no_ref', $invoice['no_ref'])
                                        ->get();

            foreach ($ledgers as $ledger) {
                $ledger->delete();
            }
        //
        //hapus data pada table stok
            $stocks = Stock::where('business_id', $business['id'])
                            ->where('no_ref', $invoice['no_ref'])
                            ->get();

            foreach ($stocks as $stock) {
                $stock->delete();
            }
        // 

        //hapus data pada table piutang
            $account_receivables = AccountReceivable::where('invoice_id', $invoice['id'])
                                                    ->get();

            if (count($account_receivables) > 0) {
                foreach ($account_receivables as $account_receivable) {
                    $account_receivable->delete();
                }
            }
        //

        //hapus data pada tabel cashflow
            $businessCashflows = Businesscashflow::where('business_id', $business['id'])
            ->where('no_ref', $invoice['no_ref'])->get();

            foreach ($businessCashflows as $businessCashflow) {
                $businessCashflow->delete();
            }
        //

        //buat data pada invoices
        $invoice->update($attributes);

        //buat data pada invoice_product
        //hapus dahulu data pada invoice_product table
        $invoice->products()->detach();

        //isi data baru pada invoice_product table
        foreach ($request->listInput as $listInput) {
            $product = Product::find($listInput['productId']);
            $invoice->products()->attach($product, ['qty' => $listInput['qty'], 'value' => $listInput['total']]);
        }

        foreach ($request->listInput as $listInput) {
            
            //akun Penjualan Berdasarkan Kategori (credit)
                $product = Product::find($listInput['productId']);
                $account_name = 'Penjualan ' . $product['category'];
                $account = Businessaccount::where('business_id', $business['id'])           
                                            ->where('name', $account_name)
                                            ->first();

                Businessledger::create([
                    'account_name' => $account['name'],
                    'account_id' => $account['id'],
                    'debit' => 0,
                    'credit' => $listInput['total'],
                    'no_ref' => $attributes['no_ref'],
                    'date' => $attributes['date'],
                    'description' => $attributes['description'],
                    'account_code' => $account['code'],
                    'author' => $request->user()->name,
                    'business_id' => $business['id']
                ]);
            //

            //lakukan loop berdasarkan list
                if ($product['is_stock_checked']) {
                    //apabila pada produk dilakukan cek stok maka lakukan transaksi dibawah:
                    //tambah kan data pada table stok dimana nilai produk(total) pada kredit, dan qty bernilai negatif karena terjadi pengurangan jumlah stok
                    //untuk HPP dilakukan metode average, yakni 
                    //1. cari tabel stok dimana qty > 1, misalkan nama variabel $stocks
                    $stocks = Stock::where('product_id', $product['id'])
                                    ->where('qty', '>', 0)
                                    ->get();

                    //2. lalu cari hpp dengan rumus $stocks->sum('debit')/$stocks->sum('qty)
                    $cogs = $stocks->sum('debit') / $stocks->sum('qty');
                    $valueProducts = $cogs * $listInput['qty'];

                    //simpan hpp pada variabel lain untuk digunakan pada input buku besar
                    Stock::create([
                        'qty' => -$listInput['qty'],
                        'unit' => $product['unit'],
                        'product_id' => $product['id'],
                        'debit' => 0,
                        'credit' => $valueProducts,
                        'no_ref' => $attributes['no_ref'],
                        'date' => $attributes['date'],
                        'business_id' => $business['id'],
                        'contact' => $attributes['contact_name'] 
                    ]);
                    
                    //Buku besar
                    //debit
                    //HPP nama akun berdasarkan kategori produk
                    $account_name = 'Harga Pokok Penjualan ' . $product['category'];
                    $account = Businessaccount::where('business_id', $business['id'])           
                                            ->where('name', $account_name)
                                            ->first();
                    Businessledger::create([
                        'account_name' => $account['name'],
                        'account_id' => $account['id'],
                        'debit' => $valueProducts,
                        'credit' => 0,
                        'no_ref' => $attributes['no_ref'],
                        'date' => $attributes['date'],
                        'description' => $attributes['description'],
                        'account_code' => $account['code'],
                        'author' => $request->user()->name,
                        'business_id' => $business['id']
                    ]);

                    //credit
                    //Persediaan nama akun berdasarkan karegori produk
                    $account_name = 'Persediaan ' . $product['category'];
                    $account = Businessaccount::where('business_id', $business['id'])           
                                            ->where('name', $account_name)
                                            ->first();
                    Businessledger::create([
                        'account_name' => $account['name'],
                        'account_id' => $account['id'],
                        'debit' => 0,
                        'credit' => $valueProducts,
                        'no_ref' => $attributes['no_ref'],
                        'date' => $attributes['date'],
                        'description' => $attributes['description'],
                        'account_code' => $account['code'],
                        'author' => $request->user()->name,
                        'business_id' => $business['id']
                    ]);
                }
            //
        }

        //(debit)
        //akun kas dan setara kas jika debit.value > 0 
        if ($request->debit['value'] > 0) {
            $account = Businessaccount::find($request->debit['account_id']);
            Businessledger::create([
                'account_name' => $account['name'],
                'account_id' => $account['id'],
                'debit' => $request->debit['value'],
                'credit' => 0,
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'description' => $attributes['description'],
                'account_code' => $account['code'],
                'author' => $request->user()->name,
                'business_id' => $business['id']
            ]);

            $account_name = 'Penjualan Produk';
            $account = Businessaccount::where('business_id', $business['id'])           
                                        ->where('name', $account_name)
                                        ->first();
            //tambahkan data pada tabel arus kas pada debit (operation)
            Businesscashflow::create([
                'account_id' => $account['id'],
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'account_code' => $account['code'],
                'account_name' => $account['name'],
                'type' => 'operation',
                'debit' => $request->debit['value'],
                'credit' => 0,
                'business_id' => $business['id']
            ]);
        }

        //akun piutang datang jika balance <= 0
        if ($request->balance < 0) {
            $account = Businessaccount::where('business_id', $business['id'])           
                                        ->where('name', 'Piutang Dagang')
                                        ->first();

            Businessledger::create([
                'account_name' => $account['name'],
                'account_id' => $account['id'],
                'debit' => -$request->balance,
                'credit' => 0,
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'description' => $attributes['description'],
                'account_code' => $account['code'],
                'author' => $request->user()->name,
                'business_id' => $business['id']
            ]);

            //masukkan data ke table account receivable
            AccountReceivable::create([
                'no_ref' => $attributes['no_ref'],
                'business_id' => $business['id'],
                'invoice_id' => $invoice['id'],
                'contact_id' => $attributes['contact_id'],
                'contact_name' => $attributes['contact_name'],
                'debit' => -$request->balance,
                'credit' => 0,
                'date' => $attributes['date'],
                'description' => $attributes['description'],
            ]);

        // 
        }

        //masukkan data ke journal
        //untuk nilai jurnal, ambul akumulatif dari nilai buku besar bedasarkan salah satu debit atau credit, dengan mengacu pada no_ref
        $ledgerValue = Businessledger::where('business_id', $business['id'])->where('no_ref', $attributes['no_ref'])->get()->sum('debit');
        
        $journal = Businessjournal::where('business_id', $business['id'])
                                    ->where('no_ref', $attributes['no_ref'])
                                    ->first();

        $journal->update([
                    'desc' => $attributes['description'],
                    'value' => $ledgerValue,
                    'detail' => 'Transaksi Penjualan',
                    'date' => $attributes['date'],
                    'author' =>   $request->user()->name,
                ]);

        return response()->json([
            'status' => 'success',
            'data' => $invoice,
        ]);
    }

    public function show(Business $business, $id, Request $request)
    {
        $invoice = Invoice::where('id', $id)
                            ->whereHas('products')
                            ->with('products')
                            ->with('contact')
                            ->first();

        $ledger = Businessledger::where('business_id', $business['id'])
                                    ->where('no_ref', $invoice['no_ref'])
                                    ->with('account')
                                    ->orderBy('account_id')
                                    ->first();

        $invoice['debit'] = $ledger;
        return response()->json([
            'status' => 'success',
            'data' => $invoice,
        ]);
    }

    public function destroy(Business $business, $id)
    {
        $invoice = Invoice::find($id);
        $invoice->delete();

        $ledgers = Businessledger::where('business_id', $business['id'])
                                ->where('no_ref', $invoice['no_ref'])
                                ->get();

        foreach ($ledgers as $ledger) {
            $ledger->delete();
        }

        $stocks = Stock::where('business_id', $business['id'])
                        ->where('no_ref', $invoice['no_ref'])
                        ->get();

        if (count($stocks) > 0) {
            foreach ($stocks as $stock) {
                $stock->delete();
            }
        }

        //cek apakah ada data pada table account receivable


        // 
        
        $journal = Businessjournal::where('business_id', $business['id'])
                            ->where('no_ref', $invoice['no_ref'])
                            ->first();

        $journal ? $journal->delete() : '';

        return response()->json([
            'status' => 'success',
            'data' => $invoice,
        ]);
    }

    public function printDetail(Business $business, $id, Request $request)
    {
        $identity = Identity::first();
        $invoice = Invoice::where('id', $id)
                    ->whereHas('products')
                    ->with('products')
                    ->with('contact')
                    ->first();

        return view('business.invoice.print-detail', compact('business', 'invoice', 'identity'));
    }
}
