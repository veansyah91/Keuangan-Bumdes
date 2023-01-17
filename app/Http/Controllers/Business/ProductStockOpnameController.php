<?php

namespace App\Http\Controllers\Business;

use App\Models\Product;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProductStockOpnameController extends Controller
{
    public function __invoke(Request $request, Business $business)
    {
        $products = Product::where('business_id', $business['id'])
                        ->filter(request(['search']))
                        ->checkStock(request(['stock_check']))
                        ->withSum('stocks', 'qty')
                        ->orderBy('name', 'asc')->paginate(50);

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }
}
