<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Business;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Models\Businessjournal;
use App\Http\Controllers\Controller;

class StockOpnameController extends Controller
{
    public function newNoRefJournal($no_ref_request, $no_ref_journal){
        $split_journal_ref_no = explode("-", $no_ref_journal);
        $old_ref_no = (int)$split_journal_ref_no[1];
        $new_ref_no = 1000000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }

    public function noRefStockOpnameRecomendation(Business $business){
        $journal = StockOpname::where('business_id', $business['id'])->orderBy('id', 'desc')->first();

        $fix_ref_no = '';

        if($journal){
            $fix_ref_no = $this->newNoRefJournal('SO', $journal->no_ref);
        }else{
            $fix_ref_no = 'SO-000001';
        }

        return response()->json([
            'status' => 'success',
            'data' => $fix_ref_no,
        ]);
    }

    public function getData(Business $business){
        return response()->json([
            'status' => 'success',
            'data' => StockOpname::filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->where('business_id', $business['id'])->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50)->withQueryString()
        ]);
    }

    public function index(Business $business)
    {
        return view('business.stock-opname.index', [
            'business' => $business, ]);
    }

    public function create(Business $business)
    {
        return view('business.stock-opname.create', [
            'business' => $business, ]);
    }

    public function store(Request $request, Business $business)
    {
        // validasi input stock opname adjustment 
        $attributes = $request->validate([
            'no_ref' => 'string|required',
            'date' => 'date|required',
            'description' => 'string|required',
        ]);
        // $attributes['list_input'] = $request->listInput;
        $attributes['desc'] = $attributes['description'];
        $attributes['source'] = 'Penyesuaian Barang';
        $attributes['business_id'] = $business['id'];
        $attributes['author'] = $request->user()->name;

        //tambah ke table stock opname
        $data = StockOpname::create($attributes);

        $total = 0;

        foreach ($request->listInput as $attribute) {
            $product = Product::find($attribute['productId']);
            $account_name = 'Persediaan ' . $product['category'];
            $account = Businessaccount::where('business_id', $business['id'])           
                                        ->where('name', $account_name)
                                        ->first();

            //hitung HPP/cogs
            //1a. hitung jumlah pembelian pada kartu stok
            $purchaseStocks = Stock::where('product_id', $product['id'])
                            ->where('qty', '>', 0)
                            ->get();
                            
            //1b. hitung jumlah penjualan pada kartu stok
            $sellingStocks = Stock::where('product_id', $product['id'])
                                ->where('qty', '<', 0)
                                ->get();

            $qtys = $purchaseStocks->sum('qty') + $sellingStocks->sum('qty');
            $values = $purchaseStocks->sum('debit') - $sellingStocks->sum('credit');

            //2. lalu cari hpp dengan rumus $stocks->sum('debit')/$stocks->sum('qty)

            $cogs = $values / $qtys;
            $valueProducts = $cogs * $attribute['qty_balance'];
            $total +=  abs($valueProducts);

            $attributes['qty'] = $attribute['qty_balance'];
            $attributes['unit'] = $product['unit'];
            $attributes['product_id'] = $product['id'];
            $attributes['debit'] = $valueProducts > 0 ? $valueProducts : 0;
            $attributes['credit'] = $valueProducts < 0 ? $valueProducts * -1 : 0;
            $attributes['account_id'] = $account['id'];
            $attributes['account_name'] = $account['name'];
            $attributes['account_id'] = $account['id'];
            $attributes['account_code'] = $account['code'];

            //buku besar
            Businessledger::create($attributes);

            //pilih akun balance
            $account = Businessaccount::find($attribute['account']['id']);
            $attributes['account_name'] = $account['name'];
            $attributes['account_id'] = $account['id'];
            $attributes['account_code'] = $account['code'];

            //simpan hpp pada variabel lain untuk digunakan pada input buku besar
            Stock::create($attributes);
            $attributes['credit'] = $valueProducts > 0 ? $valueProducts : 0;
            $attributes['debit'] = $valueProducts < 0 ? $valueProducts * -1 : 0;

            Businessledger::create($attributes);
        }

        $attributes['value'] = $total;

        //tambah ke table journal
        $journal = Businessjournal::create($attributes);            

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    public function update(Request $request, Business $business, StockOpname $stockOpname)
    {
        // validasi input stock opname adjustment 
        $attributes = $request->validate([
            'no_ref' => 'string|required',
            'date' => 'date|required',
            'description' => 'string|required',
        ]);
        $attributes['list_input'] = $request->listInput;
        $attributes['desc'] = $attributes['description'];
        $attributes['source'] = 'Penyesuaian Barang';
        $attributes['business_id'] = $business['id'];
        $attributes['author'] = $request->user()->name;

        $old_ref_no = $stockOpname['no_ref'];

        //hapus data pada ledger table
        $ledgers = Businessledger::where('business_id', $business['id'])
                            ->where('no_ref', $old_ref_no)
                            ->get();

        if (count($ledgers) > 0) {
            foreach ($ledgers as $ledger) {
                $ledger->delete();
            }
        }

        //hapus data pada stock table
        $stocks = Stock::where('business_id', $business['id'])
                        ->where('no_ref', $old_ref_no)
                        ->get();

        if (count($stocks) > 0) {
            foreach ($stocks as $stock) {
                $stock->delete();
            }
        }

        //ubah ke table stock opname
        $stockOpname->update($attributes);

        //tambah ke buku besar berdasarkan akun Persediaan Barang (debit) dan tambahkan ke table stock
        $total = 0;
        foreach ($request->listInput as $attribute) {
            $product = Product::find($attribute['productId']);
            $account_name = 'Persediaan ' . $product['category'];
            $account = Businessaccount::where('business_id', $business['id'])           
                                        ->where('name', $account_name)
                                        ->first();
            
            //hitung HPP/cogs
            //1a. hitung jumlah pembelian pada kartu stok
            $purchaseStocks = Stock::where('product_id', $product['id'])
                            ->where('qty', '>', 0)
                            ->get();
                            
            //1b. hitung jumlah penjualan pada kartu stok
            $sellingStocks = Stock::where('product_id', $product['id'])
                                ->where('qty', '<', 0)
                                ->get();

            $qtys = $purchaseStocks->sum('qty') + $sellingStocks->sum('qty');
            $values = $purchaseStocks->sum('debit') - $sellingStocks->sum('credit');

            //2. lalu cari hpp dengan rumus $stocks->sum('debit')/$stocks->sum('qty)

            $cogs = $values / $qtys;
            $valueProducts = $cogs * $attribute['qty_balance'];

            $attributes['qty'] = $attribute['qty_balance'];
            $attributes['unit'] = $product['unit'];
            $attributes['product_id'] = $product['id'];
            $attributes['debit'] = $valueProducts > 0 ? $valueProducts : 0;
            $attributes['credit'] = $valueProducts < 0 ? $valueProducts * -1 : 0;
            $attributes['account_id'] = $account['id'];
            $attributes['account_name'] = $account['name'];
            $attributes['account_id'] = $account['id'];
            $attributes['account_code'] = $account['code'];

            //buku besar
            Businessledger::create($attributes);

            //pilih akun balance
            $account = Businessaccount::find($attribute['account']['id']);
            $attributes['account_name'] = $account['name'];
            $attributes['account_id'] = $account['id'];
            $attributes['account_code'] = $account['code'];

            //simpan hpp pada variabel lain untuk digunakan pada input buku besar
            Stock::create($attributes);
            $attributes['credit'] = $valueProducts > 0 ? $valueProducts : 0;
            $attributes['debit'] = $valueProducts < 0 ? $valueProducts * -1 : 0;

            Businessledger::create($attributes);
        }

        //tambah ke table journal
        $journal = Businessjournal::where('business_id', $business['id'])->where('no_ref', $old_ref_no)->first();
        
        $journal->update([$attributes]);

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    public function edit(Business $business, StockOpname $stockOpname)
    {
        return view('business.stock-opname.edit', compact('business', 'stockOpname'));
    }

    public function show(Business $business, StockOpname $stockOpname)
    {

        //debit dari ledger sebagai list input
        $stockOpnameDetails = Stock::where('business_id', $business['id'])->where('no_ref', $stockOpname['no_ref'])->get();

        $temp = [];
        $i = 0;
        
        foreach ($stockOpnameDetails as $stock) {
            $stocks = Stock::where('id', '<', $stock['id'])->where('business_id', $business['id'])->where('product_id', $stock['product_id'])->get();

            $account = ''; //Base on product category

            $temp[$i] = [
                'productId' => $stock['product_id'],
                'productName' => $stock->product->name,
                'productCode' => $stock->product->code,
                'qty_balance' => $stock['qty'],
                'qty_book' => $stocks->sum('qty'),
                'qty_physic' => $stocks->sum('qty') + $stock['qty'],
                'account' => [
                    'id' => $stock['account_id'],
                    'name' => $stock['account_name']
                ]
            ];
            $i++;
        }

        $stockOpname['listInput'] = $temp;
        $stockOpname['created_at_for_human'] = $stockOpname->updated_at->diffForHumans();
        $stockOpname['is_updated'] = $stockOpname->updated_at != $stockOpname->created_at ? true : false;

        return response()->json([
            'status' => 'success',
            'data' => $stockOpname,
        ]);
    }

    public function printDetail(Business $business, StockOpname $stockOpname){
        //debit dari ledger sebagai list input
        $stockOpnameDetails = Stock::where('business_id', $business['id'])->where('no_ref', $stockOpname['no_ref'])->get();

        $temp = [];
        $i = 0;
        
        foreach ($stockOpnameDetails as $stock) {
            $stocks = Stock::where('business_id', $business['id'])->where('product_id', $stock['product_id'])->where('id', '<', $stock['id'])->get();

            $account = ''; //Base on product category

            $temp[$i] = [
                'productId' => $stock['product_id'],
                'productName' => $stock->product->name,
                'productCode' => $stock->product->code,
                'qty_balance' => $stock['qty'],
                'qty_book' => $stocks->sum('qty') - $stock['qty']
            ];
            $i++;
        }

        $stockOpname['listInput'] = $temp;
        $stockOpname['date_format'] = Carbon::createFromDate($stockOpname->date)->toFormattedDateString();
        $stockOpname['is_updated'] = $stockOpname->updated_at != $stockOpname->created_at ? true : false;

        return view('business.stock-opname.print-detail', compact('stockOpname', 'business'));
    }

    public function destroy(Business $business, StockOpname $stockOpname)
    {
        //hapus pada table stock opname adjustment
        $stockOpname->delete();

        //hapus pada table jurnal 
        $journal = Businessjournal::where('business_id', $business['id'])->where('no_ref', $stockOpname['no_ref'])->first();
        $journal->delete();

        //hapus pada table buku besar
        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $stockOpname['no_ref'])->get();

        if (count($ledgers) > 0) {
            foreach ($ledgers as $ledger) {
                $ledger->delete();
            }
        }

        //hapus pada table stock
        $stocks = Stock::where('business_id', $business['id'])->where('no_ref', $stockOpname['no_ref'])->get();

        if (count($stocks) > 0) {
            foreach ($stocks as $stock) {
                $stock->delete();
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $stockOpname,
        ]);
    }
}
