<?php

namespace App\Http\Controllers\Business;

use App\Models\Stock;
use App\Models\Product;
use App\Models\Business;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\PurchaseGoods;
use App\Models\AccountPayable;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Models\Businessjournal;
use App\Models\Businesscashflow;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PurchaseGoodsController extends Controller
{
    public function newNoRefPurchaseGoods($no_ref_request, $no_ref_invoice){
        $split_invoice_ref_no = explode("-", $no_ref_invoice);
        $old_ref_no = (int)$split_invoice_ref_no[1];
        $new_ref_no = 1000000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }

    public function noRefPurchaseGoodsRecomendation(Business $business){
        $fix_ref_no = '';
        $date = request('date');

        $endPurchaseGoods = PurchaseGoods::where('business_id', $business['id'])
                            ->where('no_ref', 'like', 'PG-' . $date . '%')
                            ->orderBy('id', 'desc')
                            ->first();

        $newPurchaseGoods = 'PG-' . $date . '0001';

        if ($endPurchaseGoods) {
            $split_end_invoice = explode('-', $endPurchaseGoods['no_ref']);

            $newNumber = (int)$split_end_invoice[1] + 1;

            $newPurchaseGoods = 'PG-' . $newNumber;
        }

        return response()->json([
            'status' => 'success',
            'data' => $newPurchaseGoods,
        ]);
    }

    public function getData(Business $business){
        return response()->json([
            'status' => 'success',
            'data' => PurchaseGoods::filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->where('business_id', $business['id'])->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50)->withQueryString()
        ]);
    }

    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.purchase-goods.index', [
            'business' => $business, ]);
    }

    public function create(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.purchase-goods.create', [
            'business' => $business, ]);
    }

    public function edit(Business $business, $id)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.purchase-goods.edit', [
            'business' => $business,
            'purchaseGoods' => $id
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
        $attributes['contact'] = $request->contact['name'];
        $attributes['date'] = $request->date;
        $attributes['value'] = $request->total;

        //buat data pada invoices
        $purchaseGoods = PurchaseGoods::create($attributes);

        //buat data pada invoice_product
        foreach ($request->listInput as $listInput) {
            $product = Product::find($listInput['productId']);
            $purchaseGoods->products()->save($product, ['qty' => $listInput['qty'], 'value' => $listInput['total']]);
        }

        foreach ($request->listInput as $listInput) {
            
            //akun Persediaan Berdasarkan Kategori (debit)
                $product = Product::find($listInput['productId']);
                $account_name = 'Persediaan ' . $product['category'];
                $account = Businessaccount::where('business_id', $business['id'])           
                                            ->where('name', $account_name)
                                            ->first();

                $attributes['account_id'] = $account['id'];
                $attributes['account_code'] = $account['code'];
                $attributes['account_name'] = $account['name'];
                
                $attributes['debit'] = $listInput['total'];
                $attributes['credit'] = 0;
                
                Businessledger::create($attributes);
            //
                
            $attributes['qty'] = $listInput['qty'];
            $attributes['product_id'] = $product['id'];
            $attributes['unit'] = $product['unit'];

            Stock::create($attributes);
        }

        //(credit)
        //akun kas dan setara kas jika debit.value > 0 
        if ($request->credit['value'] > 0) {
            $account = Businessaccount::find($request->credit['account_id']);
            $attributes['account_id'] = $account['id'];
            $attributes['account_code'] = $account['code'];
            $attributes['account_name'] = $account['name'];

            $attributes['credit'] = $request->credit['value'];
            $attributes['debit'] = 0;
            Businessledger::create($attributes);

            //tambahkan data pada tabel arus kas pada credit (operation)
            //cashflow  
            $attributes['type'] = 'operation';

            $total = $request->credit['value'];

            foreach ($request->listInput as $listInput) {
                $product = Product::find($listInput['productId']);
                $account_name = 'Persediaan ' . $product['category'];
                $account = Businessaccount::where('business_id', $business['id'])           
                                            ->where('name', $account_name)
                                            ->first();

                if ($listInput['total'] < $total) {
                    $attributes['credit'] = $listInput['total'];
                } else {
                    $attributes['credit'] = $total;
                }

                $attributes['account_id'] = $account['id'];
                $attributes['account_code'] = $account['code'];
                $attributes['account_name'] = $account['name'];
            
                if ($total > 0) {
                    Businesscashflow::create($attributes);
                }

                $total -= $listInput['total'];
            }
        }

        //akun utang jika balance <= 0
        if ($request->balance < 0) {
            $account = Businessaccount::where('business_id', $business['id'])           
                                        ->where('name', 'Utang Usaha')
                                        ->first();
            $attributes['account_id'] = $account['id'];
            $attributes['account_code'] = $account['code'];
            $attributes['account_name'] = $account['name'];
            
            $attributes['credit'] = -$request->balance;
            $attributes['debit'] = 0;
            $attributes['purchase_goods_id'] = $purchaseGoods['id'];
            
            Businessledger::create($attributes);

            //masukkan data ke table account receivable
                AccountPayable::create($attributes);
            // 
        }

        //masukkan data ke journal
        //untuk nilai jurnal, ambul akumulatif dari nilai buku besar bedasarkan salah satu debit atau credit, dengan mengacu pada no_ref
        $attributes['value'] = Businessledger::where('business_id', $business['id'])->where('no_ref', $attributes['no_ref'])->get()->sum('debit');
        $attributes['source'] = "Dari Faktur Pembelian Barang Dagang";
        $attributes['desc'] = $attributes['description'];

        
        $journal = Businessjournal::create($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $purchaseGoods,
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

        $purchaseGoods = PurchaseGoods::find($id);
        $no_ref = $purchaseGoods['no_ref'];
        $purchaseGoods->update($attributes);

        //hapus data pada table buku besar
            $ledgers = Businessledger::where('business_id', $business['id'])
                                        ->where('no_ref', $no_ref)
                                        ->get();

            foreach ($ledgers as $ledger) {
                $ledger->delete();
            }
        //
        //hapus data pada table stok
            $stocks = Stock::where('business_id', $business['id'])
                            ->where('no_ref', $no_ref)
                            ->get();

            foreach ($stocks as $stock) {
                $stock->delete();
            }
        // 

        //hapus data pada table piutang
            $account_receivables = AccountPayable::where('purchase_goods_id', $purchaseGoods['id'])
                                                    ->get();

            if (count($account_receivables) > 0) {
                foreach ($account_receivables as $account_receivable) {
                    $account_receivable->delete();
                }
            }
        //

        //hapus data pada tabel cashflow
            $businessCashflows = Businesscashflow::where('business_id', $business['id'])
            ->where('no_ref', $no_ref)->get();

            foreach ($businessCashflows as $businessCashflow) {
                $businessCashflow->delete();
            }
        //

        //buat data pada invoice_product
        //hapus dahulu data pada invoice_product table
        $purchaseGoods->products()->detach();

        //isi data baru pada invoice_product table
        foreach ($request->listInput as $listInput) {
            $product = Product::find($listInput['productId']);
            $purchaseGoods->products()->attach($product, ['qty' => $listInput['qty'], 'value' => $listInput['total']]);
        }

        foreach ($request->listInput as $listInput) {
            
            //akun Persediaan Berdasarkan Kategori (debit)
                $product = Product::find($listInput['productId']);
                $account_name = 'Persediaan ' . $product['category'];
                $account = Businessaccount::where('business_id', $business['id'])           
                                            ->where('name', $account_name)
                                            ->first();

                $attributes['account_id'] = $account['id'];
                $attributes['account_code'] = $account['code'];
                $attributes['account_name'] = $account['name'];
                
                $attributes['debit'] = $listInput['total'];
                $attributes['credit'] = 0;
                
                Businessledger::create($attributes);
            //
                
            $attributes['qty'] = $listInput['qty'];
            $attributes['product_id'] = $product['id'];
            $attributes['unit'] = $product['unit'];

            Stock::create($attributes);
        }

        //(credit)
        //akun kas dan setara kas jika debit.value > 0 
        if ($request->credit['value'] > 0) {
            $account = Businessaccount::find($request->credit['account_id']);
            $attributes['account_id'] = $account['id'];
            $attributes['account_code'] = $account['code'];
            $attributes['account_name'] = $account['name'];

            $attributes['credit'] = $request->credit['value'];
            $attributes['debit'] = 0;
            Businessledger::create($attributes);

            //tambahkan data pada tabel arus kas pada credit (operation)
            //cashflow  
            $attributes['type'] = 'operation';

            $total = $request->credit['value'];

            foreach ($request->listInput as $listInput) {
                $product = Product::find($listInput['productId']);
                $account_name = 'Persediaan ' . $product['category'];
                $account = Businessaccount::where('business_id', $business['id'])           
                                            ->where('name', $account_name)
                                            ->first();

                if ($listInput['total'] < $total) {
                    $attributes['credit'] = $listInput['total'];
                } else {
                    $attributes['credit'] = $total;
                }

                $attributes['account_id'] = $account['id'];
                $attributes['account_code'] = $account['code'];
                $attributes['account_name'] = $account['name'];
            
                if ($total > 0) {
                    Businesscashflow::create($attributes);
                }

                $total -= $listInput['total'];
            }
        }

        //akun piutang datang jika balance <= 0
        if ($request->balance < 0) {
            $account = Businessaccount::where('business_id', $business['id'])           
                                        ->where('name', 'Utang Usaha')
                                        ->first();
            $attributes['account_id'] = $account['id'];
            $attributes['account_code'] = $account['code'];
            $attributes['account_name'] = $account['name'];
            
            $attributes['credit'] = -$request->balance;
            $attributes['debit'] = 0;
            $attributes['purchase_goods_id'] = $purchaseGoods['id'];
            
            Businessledger::create($attributes);

            //masukkan data ke table account receivable
                AccountPayable::create($attributes);
            // 
        }  

        //masukkan data ke journal
        //untuk nilai jurnal, ambul akumulatif dari nilai buku besar bedasarkan salah satu debit atau credit, dengan mengacu pada no_ref
        $attributes['value'] = Businessledger::where('business_id', $business['id'])->where('no_ref', $attributes['no_ref'])->get()->sum('debit');
        $attributes['source'] = "Dari Faktur Pembelian Barang Dagang";
        $attributes['desc'] = $attributes['description'];
        
        $journal = Businessjournal::where('business_id', $business['id'])
                                    ->where('no_ref', $no_ref)
                                    ->first();

        $journal->update($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $purchaseGoods,
        ]);
    }

    public function show(Business $business, $id, Request $request)
    {
        $purchaseGoods = PurchaseGoods::where('id', $id)
                            ->whereHas('products')
                            ->with('products')
                            ->with('contact')
                            ->first();

        $ledger = Businessledger::where('business_id', $business['id'])
                                    ->where('no_ref', $purchaseGoods['no_ref'])
                                    ->with('account')
                                    ->orderBy('account_id')
                                    ->first();

        $purchaseGoods['credit'] = $ledger;
        return response()->json([
            'status' => 'success',
            'data' => $purchaseGoods,
        ]);
    }

    public function destroy(Business $business, $id)
    {
        $purchaseGoods = PurchaseGoods::find($id);
        $purchaseGoods->delete();

        $ledgers = Businessledger::where('business_id', $business['id'])
                                ->where('no_ref', $purchaseGoods['no_ref'])
                                ->get();

        foreach ($ledgers as $ledger) {
            $ledger->delete();
        }

        $stocks = Stock::where('business_id', $business['id'])
                        ->where('no_ref', $purchaseGoods['no_ref'])
                        ->get();

        if (count($stocks) > 0) {
            foreach ($stocks as $stock) {
                $stock->delete();
            }
        }
        
        $journal = Businessjournal::where('business_id', $business['id'])
                            ->where('no_ref', $purchaseGoods['no_ref'])
                            ->first();

        $journal ? $journal->delete() : '';

        //hapus data pada table cashflow
        $cashflows = Businesscashflow::whereBusinessId($business['id'])
                                        ->whereNoRef($purchaseGoods['no_ref'])
                                        ->get();

        if (count($cashflows) > 0) {
            foreach ($cashflows as $cashflow) {
                $cashflow->delete();
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $purchaseGoods,
        ]);
    }

    public function printDetail(Business $business, $id, Request $request)
    {
        $identity = Identity::first();
        $purchaseGoods = PurchaseGoods::where('id', $id)
                    ->whereHas('products')
                    ->with('products')
                    ->with('contact')
                    ->first();

        return view('business.purchase-goods.print-detail', compact('business', 'purchaseGoods', 'identity'));
    }
}
