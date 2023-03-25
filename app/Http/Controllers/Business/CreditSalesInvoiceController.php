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
use App\Models\CreditApplication;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreditSalesInvoiceController extends Controller
{
    public function noRefCreditSalesInvoiceRecomendation(Business $business){
        $fix_ref_no = '';

        $date = str_replace('-', '', request('date'));

        $endCreditSales = Invoice::where('business_id', $business['id'])
                            ->where('no_ref', 'like', 'CS-'. $date . '%')
                            ->orderBy('id', 'desc')
                            ->first();

        $newAccountPayable = 'CS-' . $date . '0001';

        if ($endCreditSales) {
            $split_end_invoice = explode('-', $endCreditSales['no_ref']);

            $newNumber = (int)$split_end_invoice[1] + 1;

            $newAccountPayable = 'CS-' . $newNumber;
        }

        return response()->json([
            'status' => 'success',
            'data' => $newAccountPayable,
        ]);
    }

    public function getData(Business $business)
    {

        return response()->json([
            'status' => 'success',
            'data' => AccountReceivable::filter(request(['date_from','date_to','this_week','this_month','this_year', 'search']))
                                ->whereBusinessId($business['id'])
                                ->where('debit', '>', 0)
                                ->whereCategory('credit')
                                ->with('contact')
                                ->with('invoice')
                                ->latest()
                                ->paginate(50),
        ]);
    }

    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }

        $identity = Identity::first();

        return view('business.credit-sales.index', ['business' => $business, 'identity' => $identity, ]);
    }

    public function store(Business $business, Request $request)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'value' => 'required|numeric',
            'downpayment' => 'numeric',
            'tenor' => 'numeric',
            'profit' => 'numeric',
            'other_cost' => 'numeric',
        ]); 

        if (!$request->contact['id']) {
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        $attributes['contact_id'] = $request->contact['id'];
        $attributes['business_id'] = $request->business['id'];
        $attributes['contact_name'] = $request->contact['name'];
        $attributes['contact'] = $request->contact['name'];
        $attributes['debit'] = $attributes['value'];
        $attributes['credit'] = 0;
        $attributes['category'] = 'credit';
        $attributes['description'] = 'Pemberian Kredit Kepada ' . $attributes['contact_name'];
        $attributes['desc'] = 'Pemberian Kredit Kepada ' . $attributes['contact_name'];
        $attributes['product_id'] = $request->product['id'];
        $tempRef = $attributes['no_ref'];
        
        $attributes['author'] = $request->user()->name;

        if ($request->credit_application['id']) {
            $attributes['credit_application_id'] = $request->credit_application['id'];

            $attributes['status'] = 'approved';
            
            $creditApplication = CreditApplication::find($attributes['credit_application_id']);
            $attributes['no_ref'] = $creditApplication['no_ref'];

            $creditApplication->update($attributes);
            $attributes['credit_application_id'] = $creditApplication['id'];
        } 
        else {
            $fix_ref_no = '';

            $date = str_replace('-', '', $attributes['date']);

            $endCreditApplication = CreditApplication::where('business_id', $business['id'])
                                ->where('no_ref', 'like', 'CA-'. $date . '%')
                                ->orderBy('id', 'desc')
                                ->first();

            $attributes['no_ref'] = 'CA-' . $date . '0001';

            if ($endCreditApplication) {
                $split_end_invoice = explode('-', $endCreditApplication['no_ref']);

                $newNumber = (int)$split_end_invoice[1] + 1;

                $attributes['no_ref'] = 'CA-' . $newNumber;
            }

            $attributes['status'] = 'approved';

            $creditApplication = CreditApplication::create($attributes);

            $attributes['credit_application_id'] = $creditApplication['id'];
        }

        $attributes['status'] = 0;

        $attributes['no_ref'] = $tempRef ;

        $invoice = Invoice::create($attributes);

        $attributes['invoice_id'] = $invoice['id'];

        $product = Product::find($request->product['id']);

        $invoice->products()->save($product, ['qty' => 1, 'value' => $attributes['value']]);

        //cek stok
        $stocks = Stock::whereBusinessId($business['id'])
                        ->whereProductId($product['id'])
                        ->get();

        $total_stock = $stocks->sum('qty');
        $valueProducts = $total_stock > 0 ? $stocks->sum('debit') / $stocks->sum('qty') : 0;

        $account_name = 'Persediaan ' . $product['category'];
        $account = Businessaccount::where('business_id', $business['id'])           
                                    ->where('name', $account_name)
                                    ->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];
        $attributes['debit'] = 0;
        $attributes['credit'] = $total_stock > 0 ? $valueProducts : $product['unit_price'];

        //jika stok tersedia lebih dari 0 maka
        if ($total_stock > 0) {
            //1. Masukkan data pada stock dengan nilai stok = -1
            $attributes['product_id'] = $product['id'];
            $attributes['unit'] = 'pcs';
            $attributes['qty'] = -1;
            Stock::create($attributes);
        }
        
        //2. Buat data di buku besar pada credit dengan akun Persediaan (Kategori Produk)
        Businessledger::create($attributes);
        
        //3. Buat data di buku besar pada debit dengan akun Harga Pokok Penjualan (Kategori Produk)
        $account_name = 'Harga Pokok Penjualan ' . $product['category'];
        $account = Businessaccount::where('business_id', $business['id'])           
                                ->where('name', $account_name)
                                ->first();

        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];                       

        $attributes['debit'] = $total_stock > 0 ? $valueProducts : $product['unit_price'];
        $attributes['credit'] = 0;
        Businessledger::create($attributes);

        $attributes['debit'] = $attributes['value'] - $attributes['downpayment'];
        $attributes['credit'] = 0;

        $attributes['due_date_temp'] = date('Y-m-d', strtotime('+1 month', strtotime($attributes['date'])));
        
        $accountReceivable = AccountReceivable::create($attributes);

        //ledger
        //akun debit => piutang
        $account = Businessaccount::where('business_id', $business['id'])
                                    ->where('name', 'Piutang Dagang')
                                    ->first();

        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];

        Businessledger::create($attributes);

        //akun credit => pada akun penjualan
        $account_name = 'Penjualan ' . $product['category'];
        $account = Businessaccount::where('business_id', $business['id'])           
                                ->where('name', $account_name)
                                ->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];
        $attributes['credit'] = $attributes['value'];
        $attributes['debit'] = 0;

        Businessledger::create($attributes);

        if ($attributes['downpayment'] > 0) {
            //cashflow
            $attributes['debit'] = $attributes['downpayment'];
            $attributes['credit'] = 0;
            $attributes['type'] = 'operation';
            Businesscashflow::create($attributes);

            //debit pada akun kas
            $account = Businessaccount::find($request->account['id']);
            $attributes['account_id'] = $account['id'];
            $attributes['account_name'] = $account['name'];
            $attributes['account_code'] = $account['code'];

            Businessledger::create($attributes);
        }
        
        $attributes['source'] = 'Kredit Nasabah';

        //journal
        Businessjournal::create($attributes);

        $attributes['id'] = $accountReceivable['id'];

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    public function show(Business $business, $id)
    {
        $data = AccountReceivable::whereId($id)
                                    ->with('contact', fn($query) => 
                                        $query->with('detail')    
                                    )
                                    ->with('invoice', fn($query) => 
                                        $query->with('products')    
                                    )
                                    ->with('creditApplication')
                                    ->first();

        //cek dp dengan akun kas pada buku besar
        $businessCashflow = Businesscashflow::whereBusinessId($business['id'])
                                                ->whereNoRef($data['no_ref'])
                                                ->first();

                                                
        $data['downpayment'] = $businessCashflow ? $businessCashflow['debit'] : 0;

        //cek akun kas
        if ($data['downpayment'] > 0) {
            $ledger = Businessledger::whereBusinessId($business['id'])
                            ->where('debit', '>', 0)
                            ->where(fn($query) => 
                                $query->where('account_code', 'like', '11%')
                                        ->orWhere('account_code', 'like', '12%')
                            )
                            ->first();

            $data['account'] = [
                'id' => $ledger['account_id'],
                'name' => $ledger['account_name'],
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function update(Request $request, Business $business, $id)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'value' => 'required|numeric',
            'downpayment' => 'numeric',
            'tenor' => 'numeric',
            'profit' => 'numeric',
            'other_cost' => 'numeric',
        ]); 

        if (!$request->contact['id']) {
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }
        
        $attributes['contact_id'] = $request->contact['id'];
        $attributes['business_id'] = $request->business['id'];
        $attributes['contact_name'] = $request->contact['name'];
        $attributes['contact'] = $request->contact['name'];
        $attributes['debit'] = $attributes['value'];
        $attributes['credit'] = 0;
        $attributes['category'] = 'credit';
        $attributes['description'] = 'Pemberian Kredit Kepada ' . $attributes['contact_name'];
        $attributes['desc'] = 'Pemberian Kredit Kepada ' . $attributes['contact_name'];
        $attributes['product_id'] = $request->product['id'];
        
        $attributes['author'] = $request->user()->name;

        $accountReceivable = AccountReceivable::find($id);
        
        $invoice = Invoice::find($accountReceivable['invoice_id']);
        $tempRef = $invoice['no_ref'];        

        //atur table credit application ke pending lagi
        $creditApplication = CreditApplication::find($accountReceivable['credit_application_id']);

        $creditApplication->update(
            ['status' => 'pending']
        );
        
        if ($request->credit_application['id']) {
            $attributes['credit_application_id'] = $request->credit_application['id'];

            $attributes['status'] = 'approved';
            
            $creditApplication = CreditApplication::find($attributes['credit_application_id']);
            $attributes['no_ref'] = $creditApplication['no_ref'];

            $creditApplication->update($attributes);
            $attributes['credit_application_id'] = $creditApplication['id'];
        } 
        else {
            $fix_ref_no = '';

            $date = str_replace('-', '', $attributes['date']);

            $endCreditApplication = CreditApplication::where('business_id', $business['id'])
                                ->where('no_ref', 'like', 'CA-'. $date . '%')
                                ->orderBy('id', 'desc')
                                ->first();

            $attributes['no_ref'] = 'CA-' . $date . '0001';

            if ($endCreditApplication) {
                $split_end_invoice = explode('-', $endCreditApplication['no_ref']);

                $newNumber = (int)$split_end_invoice[1] + 1;

                $attributes['no_ref'] = 'CA-' . $newNumber;
            }

            $attributes['status'] = 'approved';

            $creditApplication = CreditApplication::create($attributes);

            $attributes['credit_application_id'] = $creditApplication['id'];
        }

        $attributes['status'] = 0;

        $attributes['no_ref'] = $tempRef ;

        //hapus dahulu data pada invoice_product table
        $invoice->products()->detach();

        $invoice->update($attributes);
        $attributes['due_date_temp'] = date('Y-m-d', strtotime('+1 month', strtotime($attributes['date'])));

        $accountReceivable->update($attributes);

        $product = Product::find($request->product['id']);

        $invoice->products()->save($product, ['qty' => 1, 'value' => $attributes['value']]);

        //cek stok
        $stock = Stock::whereBusinessId($business['id'])
                ->whereProductId($product['id'])
                ->whereNoRef($tempRef)
                ->first();

        if ($stock) {
            $stock->delete();
        }

        // hapus ledger 
        $businessLegders = Businessledger::whereBusinessId($business['id'])
                                        ->whereNoRef($tempRef)
                                        ->get();

        if (count($businessLegders) > 0) {
            foreach ($businessLegders as $businessLegder) {
                $businessLegder->delete();
            }
        }

        $cashflow = Businesscashflow::whereBusinessId($business['id'])
                                    ->whereNoRef($tempRef)
                                    ->first();

        if ($cashflow) {
            $cashflow->delete();
        }

        //cek stok
        $stocks = Stock::whereBusinessId($business['id'])
                        ->whereProductId($product['id'])
                        ->get();

        $total_stock = $stocks->sum('qty');
        $valueProducts = $total_stock > 0 ? $stocks->sum('debit') / $stocks->sum('qty') : 0;

        $account_name = 'Persediaan ' . $product['category'];
        $account = Businessaccount::where('business_id', $business['id'])           
                                    ->where('name', $account_name)
                                    ->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];
        $attributes['debit'] = 0;
        $attributes['credit'] = $total_stock > 0 ? $valueProducts : $product['unit_price'];

        //jika stok tersedia lebih dari 0 maka
        if ($total_stock > 0) {
            //1. Masukkan data pada stock dengan nilai stok = -1
            $attributes['product_id'] = $product['id'];
            $attributes['unit'] = 'pcs';
            $attributes['qty'] = -1;
            Stock::create($attributes);
        }
        
        //2. Buat data di buku besar pada credit dengan akun Persediaan (Kategori Produk)
        Businessledger::create($attributes);
        
        //3. Buat data di buku besar pada debit dengan akun Harga Pokok Penjualan (Kategori Produk)
        $account_name = 'Harga Pokok Penjualan ' . $product['category'];
        $account = Businessaccount::where('business_id', $business['id'])           
                                ->where('name', $account_name)
                                ->first();

        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];                       

        $attributes['debit'] = $total_stock > 0 ? $valueProducts : $product['unit_price'];
        $attributes['credit'] = 0;
        Businessledger::create($attributes);

        $attributes['debit'] = $attributes['value'] - $attributes['downpayment'];
        $attributes['credit'] = 0;

        $accountReceivable->update($attributes);

        //ledger
        //akun debit => piutang
        $account = Businessaccount::where('business_id', $business['id'])
                                    ->where('name', 'Piutang Dagang')
                                    ->first();

        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];

        Businessledger::create($attributes);

        //akun credit => pada akun penjualan
        $account_name = 'Penjualan ' . $product['category'];
        $account = Businessaccount::where('business_id', $business['id'])           
                                ->where('name', $account_name)
                                ->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];
        $attributes['credit'] = $attributes['value'];
        $attributes['debit'] = 0;

        Businessledger::create($attributes);

        if ($attributes['downpayment'] > 0) {
            //cashflow
            $attributes['debit'] = $attributes['downpayment'];
            $attributes['credit'] = 0;
            $attributes['type'] = 'operation';
            Businesscashflow::create($attributes);

            //debit pada akun kas
            $account = Businessaccount::find($request->account['id']);
            $attributes['account_id'] = $account['id'];
            $attributes['account_name'] = $account['name'];
            $attributes['account_code'] = $account['code'];

            Businessledger::create($attributes);
        }
        
        $attributes['source'] = 'Kredit Nasabah';

        //journal 
        $journal = Businessjournal::whereBusinessId($business['id'])
                                    ->whereNoRef($tempRef)
                                    ->first();

        $journal->update($attributes); 

        $attributes['id'] = $id;

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]);
    }

    public function destroy(Business $business, $id)
    {
        $data = AccountReceivable::findOrFail($id);

        $accountReceivables = AccountReceivable::whereBusinessId($business['id'])
                            ->where('invoice_id', $data['invoice_id'])
                            ->get();

        if (count($accountReceivables) > 1) {
            throw ValidationException::withMessages([
                'message' => 'Data TIdak Bisa Dihapus, Data Telah Digunakan'
            ]);
        }

        if ($data['credit_application_id']) {
            $creditApplication = CreditApplication::find($data['credit_application_id']);

            $creditApplication->update([
                'status' => 'pending'
            ]);
        }

        $journals = Businessjournal::whereBusinessId($business['id'])
                                    ->whereNoRef($data['no_ref'])  
                                    ->get();

        if (count($journals) > 0) {
            foreach ($journals as $journal) {
                $journal->delete();
            }
        }

        $ledgers = Businessledger::whereBusinessId($business['id'])
                                ->whereNoRef($data['no_ref'])  
                                ->get();

        if (count($ledgers) > 0) {
            foreach ($ledgers as $ledger) {
                $ledger->delete();
            }
        }

        $cashflows = Businesscashflow::whereBusinessId($business['id'])
                                ->whereNoRef($data['no_ref'])  
                                ->get();

        if (count($cashflows) > 0) {
            foreach ($cashflows as $cashflow) {
                $cashflow->delete();
            }
        }

        $data->delete();

        $invoice = Invoice::findOrFail($data['invoice_id']);

        $invoice->delete();
        
        //cek stock
        $stocks = Stock::whereBusinessId($business['id'])
                        ->whereNoRef($invoice['no_ref'])
                        ->get();

        if (count($stocks) > 0) {
            foreach ($stocks as $stock) {
                $stock->delete();
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $invoice,
        ]);
    }

    public function printDetail(Business $business, $id, Request $request)
    {
        $identity = Identity::first();
        $accountReceivable = AccountReceivable::where('id', $id)
                    ->whereHas('creditApplication')
                    ->with('creditApplication')
                    ->with('contact')
                    ->first();

        return view('business.credit-sales.print-detail', compact('business', 'accountReceivable', 'identity'));
    }

    public function card(Business $business, $id)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        $identity = Identity::first();

        $accountReceivable = AccountReceivable::find($id);

        $payments = AccountReceivable::whereBusinessId($business['id'])   
                                        ->whereInvoiceId($accountReceivable['invoice_id'])
                                        ->where('credit', '>', 0)
                                        ->whereCategory('credit')
                                        ->with('contact')
                                        ->get();

                                        // dd($accountReceivable->invoice());


        return view('business.credit-sales.card', [
            'business' => $business, 
            'accountReceivable' => $accountReceivable, 
            'payments' => $payments, 
            'identity' => $identity, 
        ]);
    }
}