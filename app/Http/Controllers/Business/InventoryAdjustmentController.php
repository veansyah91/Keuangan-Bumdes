<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Models\Businessjournal;
use App\Models\InventoryAdjustment;
use App\Http\Controllers\Controller;

class InventoryAdjustmentController extends Controller
{
    public function newNoRefJournal($no_ref_request, $no_ref_journal){
        $split_journal_ref_no = explode("-", $no_ref_journal);
        $old_ref_no = (int)$split_journal_ref_no[1];
        $new_ref_no = 1000000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }

    public function noRefInventoryAdjustmentRecomendation(Business $business){
        $journal = InventoryAdjustment::where('business_id', $business['id'])->orderBy('id', 'desc')->first();

        $fix_ref_no = '';

        if($journal){
            $fix_ref_no = $this->newNoRefJournal('IA', $journal->no_ref);
        }else{
            $fix_ref_no = 'IA-000001';
        }

        return response()->json([
            'status' => 'success',
            'data' => $fix_ref_no,
        ]);
    }

    public function getData(Business $business){
        return response()->json([
            'status' => 'success',
            'data' => InventoryAdjustment::filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->where('business_id', $business['id'])->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50)->withQueryString()
        ]);
    }

    public function index(Business $business)
    {
        return view('business.inventory-adjustment.index', [
            'business' => $business, ]);
    }

    public function create(Business $business)
    {
        return view('business.inventory-adjustment.create', [
            'business' => $business, ]);
    }

    public function store(Request $request, Business $business)
    {
        // validasi input inventory adjustment 
        $attributes = $request->validate([
            'no_ref' => 'string|required',
            'date' => 'date|required',
            'description' => 'string|required',
        ]);
        $attributes['list_input'] = $request->listInput;
        $attributes['credit'] = $request->credit;
        $attributes['desc'] = $attributes['description'];
        $attributes['source'] = 'Penyesuaian Barang';
        $attributes['business_id'] = $business['id'];
        $attributes['author'] = $request->user()->name;

        //tambah ke table inventory
        $data = InventoryAdjustment::create($attributes);

        //tambah ke buku besar berdasarkan akun Persediaan Barang (debit) dan tambahkan ke table stock
        $total = 0;
        foreach ($attributes['list_input'] as $attribute) {
            $name = 'Persediaan ' . $attribute['productCategory'];
            $account = Businessaccount::where('business_id', $business['id'])->where('name', $name)->first();

            $total += $attribute['total'];

            //buku besar
            Businessledger::create([
                'account_name' => $account['name'],
                'account_id' => $account['id'],
                'debit' => $attribute['total'],
                'credit' => 0,
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'description' => $attributes['desc'],
                'account_code' => $account['code'],
                'author' => $request->user()->name,
                'business_id' => $business['id']
            ]);

            //tambahkan ke table stock
            $product = Product::where('id', $attribute['productId'])->first();

            Stock::create([
                'qty' => $attribute['qty'],
                'unit' => $product['unit'],
                'product_id' => $product['id'],
                'debit' => $attribute['total'],
                'credit' => 0,
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'business_id' => $business['id']
            ]);
        }

        //tambah ke buku besar berdasarkan akun kredit
        $account = Businessaccount::where('business_id', $business['id'])->where('id', $attributes['credit']['id'])->first();
        Businessledger::create([
            'account_name' => $account['name'],
            'account_id' => $account['id'],
            'debit' => 0,
            'credit' => $total,
            'no_ref' => $attributes['no_ref'],
            'date' => $attributes['date'],
            'description' => $attributes['desc'],
            'account_code' => $account['code'],
            'author' =>$request->user()->name,
            'business_id' => $business['id']
        ]);

        $attributes['value'] = $total;

        //tambah ke table journal
        $journal = Businessjournal::create($attributes);            

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    public function update(Request $request, Business $business, InventoryAdjustment $inventoryadjustment)
    {
        // validasi input inventory adjustment 
        $attributes = $request->validate([
            'no_ref' => 'string|required',
            'date' => 'date|required',
            'description' => 'string|required',
        ]);
        $attributes['list_input'] = $request->listInput;
        $attributes['credit'] = $request->credit;
        $attributes['desc'] = $attributes['description'];
        $attributes['source'] = 'Penyesuaian Barang';
        $attributes['business_id'] = $business['id'];
        $attributes['author'] = $request->user()->name;

        //hapus data pada ledger table dimana debit > 0
        $ledgers = Businessledger::where('business_id', $business['id'])
                            ->where('no_ref', $attributes['no_ref'])
                            ->where('debit', '>', 0)
                            ->get();

        if (count($ledgers) > 0) {
            foreach ($ledgers as $ledger) {
                $ledger->delete();
            }
        }

        //hapus data pada stock table dimana debit > 0
        $stocks = Stock::where('business_id', $business['id'])
                        ->where('no_ref', $attributes['no_ref'])
                        ->where('debit', '>', 0)
                        ->get();

        if (count($stocks) > 0) {
            foreach ($stocks as $stock) {
                $stock->delete();
            }
        }

        //ubah ke table inventory
        $inventoryadjustment->update($attributes);

        //tambah ke buku besar berdasarkan akun Persediaan Barang (debit) dan tambahkan ke table stock
        $total = 0;
        foreach ($attributes['list_input'] as $attribute) {
            $name = 'Persediaan ' . $attribute['productCategory'];
            $account = Businessaccount::where('business_id', $business['id'])->where('name', $name)->first();

            $total += $attribute['total'];

            //buku besar
            Businessledger::create([
                'account_name' => $account['name'],
                'account_id' => $account['id'],
                'debit' => $attribute['total'],
                'credit' => 0,
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'description' => $attributes['desc'],
                'account_code' => $account['code'],
                'author' => $request->user()->name,
                'business_id' => $business['id']
            ]);

            //tambahkan ke table stock
            $product = Product::where('id', $attribute['productId'])->first();

            Stock::create([
                'qty' => $attribute['qty'],
                'unit' => $product['unit'],
                'product_id' => $product['id'],
                'debit' => $attribute['total'],
                'credit' => 0,
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'business_id' => $business['id']
            ]);
        }

        //buat ke buku besar berdasarkan akun kredit
        $ledger = Businessledger::where('business_id', $business['id'])
                            ->where('no_ref', $attributes['no_ref'])
                            ->where('credit', '>', 0)
                            ->first();

        $account = Businessaccount::where('business_id', $business['id'])->where('id', $attributes['credit']['id'])->first();

        $ledger->update([
            'account_name' => $account['name'],
            'account_id' => $account['id'],
            'debit' => 0,
            'credit' => $total,
            'no_ref' => $attributes['no_ref'],
            'date' => $attributes['date'],
            'description' => $attributes['desc'],
            'account_code' => $account['code'],
            'author' =>$request->user()->name,
            'business_id' => $business['id']
        ]);

        $attributes['value'] = $total;

        //tambah ke table journal
        $journal = Businessjournal::where('business_id', $business['id'])->where('no_ref', $attributes['no_ref'])->first();
        
        $journal->update([$attributes]);

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    public function edit(Business $business, InventoryAdjustment $inventoryadjustment)
    {
        return view('business.inventory-adjustment.edit', compact('business', 'inventoryadjustment'));
    }

    public function show(Business $business, InventoryAdjustment $inventoryadjustment)
    {
        //credit dari ledger
        $ledger = Businessledger::where('business_id', $business['id'])->where('no_ref', $inventoryadjustment['no_ref'])->where('credit', '>', 0)->first();

        $inventoryadjustment['credit'] = [
            'id' => $ledger['account_id'],
            'name' => $ledger['account_name'],
        ];

        //debit dari ledger sebagai list input
        $stocks = Stock::where('business_id', $business['id'])->where('no_ref', $inventoryadjustment['no_ref'])->where('debit', '>', 0)->get();

        $temp = [];
        $i = 0;
        
        foreach ($stocks as $stock) {
            $temp[$i] = [
                'productId' => $stock['product_id'],
                'productName' => $stock->product->name,
                'productCategory' => $stock->product->category,
                'productCode' => $stock->product->code,
                'qty' => $stock['qty'],
                'unit_price' => $stock['debit']/$stock['qty'],
                'total' => $stock['debit']
            ];
            $i++;
        }

        $inventoryadjustment['listInput'] = $temp;
        $inventoryadjustment['created_at_for_human'] = $inventoryadjustment->updated_at->diffForHumans();
        $inventoryadjustment['is_updated'] = $inventoryadjustment->updated_at != $inventoryadjustment->created_at ? true : false;

        return response()->json([
            'status' => 'success',
            'data' => $inventoryadjustment,
        ]);
    }

    public function printDetail(Business $business, InventoryAdjustment $inventoryadjustment){
        //credit dari ledger
        $ledger = Businessledger::where('business_id', $business['id'])->where('no_ref', $inventoryadjustment['no_ref'])->where('credit', '>', 0)->first();

        $inventoryadjustment['credit'] = [
            'id' => $ledger['account_id'],
            'name' => $ledger['account_name'],
        ];

        //debit dari ledger sebagai list input
        $stocks = Stock::where('business_id', $business['id'])->where('no_ref', $inventoryadjustment['no_ref'])->where('debit', '>', 0)->get();

        $temp = [];
        $i = 0;
        
        foreach ($stocks as $stock) {
            $temp[$i] = [
                'productId' => $stock['product_id'],
                'productName' => $stock->product->name,
                'productCategory' => $stock->product->category,
                'productCode' => $stock->product->code,
                'qty' => $stock['qty'],
                'unit_price' => $stock['debit']/$stock['qty'],
                'total' => $stock['debit']
            ];
            $i++;
        }

        $inventoryadjustment['listInput'] = $temp;
        $inventoryadjustment['date_format'] = Carbon::createFromDate($inventoryadjustment->date)->toFormattedDateString();
        $inventoryadjustment['is_updated'] = $inventoryadjustment->updated_at != $inventoryadjustment->created_at ? true : false;

        return view('business.inventory-adjustment.print-detail', compact('inventoryadjustment', 'business'));
    }

    public function destroy(Business $business, InventoryAdjustment $inventoryadjustment)
    {
        //hapus pada table inventory adjustment
        $inventoryadjustment->delete();

        //hapus pada table jurnal 
        $journal = Businessjournal::where('business_id', $business['id'])->where('no_ref', $inventoryadjustment['no_ref'])->first();
        $journal->delete();

        //hapus pada table buku besar
        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $inventoryadjustment['no_ref'])->get();

        if (count($ledgers) > 0) {
            foreach ($ledgers as $ledger) {
                $ledger->delete();
            }
        }

        //hapus pada table stock
        $stocks = Stock::where('business_id', $business['id'])->where('no_ref', $inventoryadjustment['no_ref'])->get();

        if (count($stocks) > 0) {
            foreach ($stocks as $stock) {
                $stock->delete();
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $inventoryadjustment,
        ]);
    }
}
