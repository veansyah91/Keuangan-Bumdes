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
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function index(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        } 

        return view('business.product.index', [
            'business' => $business, ]);
    }

    public function create(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        } 
        
        return view('business.product.create', compact('business'));
    }

    public function edit(Business $business,Product $product, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        } 
        
        return view('business.product.edit', compact('business', 'product'));
    }

    public function store(Business $business, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'code' => 'required',
            'category' => 'required',
            'supplier' => 'required',
            'unit' => 'required',
            'unit_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
        ]);

        $validated['business_id'] = $business['id'];
        $validated['is_active'] = $request['is_active'];
        $validated['is_stock_checked'] = $request['is_stock_checked'];

        $product = Product::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }

    public function update(Business $business, Product $product, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'code' => 'required',
            'category' => 'required',
            'supplier' => 'required',
            'unit' => 'required',
            'unit_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
        ]);

        $validated['business_id'] = $business['id'];
        $validated['is_active'] = $request['is_active'];
        $validated['is_stock_checked'] = $request['is_stock_checked'];

        $product->update($validated);
        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }

    public function destroy(Business $business, Product $product, Request $request)
    {
        //cek apakah produk sudah ada pada data stock atau invoice
        //jika ada maka tampilkan error bahwa data produk tidak bisa dihapus

        //cek pada stok
        $stocks = Stock::where('product_id', $product['id'])->first();
        
        //cek pada invoice
        if ($stocks) {
            throw ValidationException::withMessages([
                'message' => ["Tidak Bisa Dihapus, Produk Telah Digunakan Pada Stok"]
            ]);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
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
        $products = Product::where('business_id', $business['id'])
                        ->withSum('stocks', 'qty')
                        ->withSum('stocks', 'debit')
                        ->withSum('stocks', 'credit')
                        ->filter(request(['search']))
                        ->checkStock(request(['stock_check']))
                        ->orderBy('name', 'asc')->paginate(50);

        $response = [
            'message' => "Data Telah Tervalidasi",
            'status' => 'Success',
            'data' => $products
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

    public function newNoRefProduct($no_ref_request, $no_ref_product){
        $split_product_ref_no = explode("-", $no_ref_product);
        $old_ref_no = (int)$split_product_ref_no[1];
        $new_ref_no = 1000000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }

    public function noRefProductRecomendation(Business $business){
        $ref_no = explode("-", request('search'));
        $product = Product::filter(request(['search']))->where('business_id', $business['id'])->orderBy('id', 'desc')->first();

        $fix_ref_no = '';

        if($product){
            $fix_ref_no = $this->newNoRefProduct($ref_no[0], $product->code);
        }else{
            $fix_ref_no = $ref_no[0] . '-000001';
        }

        return response()->json([
            'status' => 'success',
            'data' => $fix_ref_no,
        ]);
    }

    public function print(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        } 

        $products = Product::where('business_id', $business['id'])
                            ->withSum('stocks', 'qty')
                            ->orderBy('name')
                            ->get();
        
        return view('business.product.print',[
            'products' => $products,
            'business' => $business,
            'author' => request()->user()
        ]);
    }

    public function printStock(Business $business, Request $request)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        } 

        $products = Product::where('business_id', $business['id'])
                            ->whereHas('stocks')
                            ->with('stocks', function($query) {
                                $query->filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date');
                            })
                            ->orderBy('name')
                            ->get();

                            $period = '';
        if (request('date_from') && request('date_to')) {
            $period = request('date_from') == request('date_to') ? Carbon::parse(request('date_from'))->isoformat('MMM, D Y') : Carbon::parse(request('date_from'))->isoformat('MMM, D Y') . ' - ' . Carbon::parse(request('date_to'))->isoformat('MMM, D Y');
        } elseif (request('this_week')) {
            $period = Carbon::parse(now()->startOfWeek())->isoformat('MMM, D Y') . ' - ' . Carbon::parse(now()->endOfWeek())->isoformat('MMM, D Y');
            
        } elseif (request('this_month'))
        {
            $period = Carbon::now()->isoformat('MMMM, Y');
        } else{
            $period = Carbon::now()->isoformat('Y');
        }

        return view('business.product.print-stock',[
            'products' => $products,
            'business' => $business,
            'author' => request()->user(),
            'period' => $period,
            'request' => $request
        ]);
    }
}
