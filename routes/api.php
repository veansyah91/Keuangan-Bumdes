<?php

use App\Models\Income;
use App\Models\Outcome;
use App\Models\Regency;
use App\Models\Village;
use App\Models\Business;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Business\AssetController;
use App\Http\Controllers\Business\BrandController;
use App\Http\Controllers\Business\StockController;
use App\Http\Controllers\Business\CashierController;
use App\Http\Controllers\Business\ProductController;
use App\Http\Controllers\Business\CategoryController;
use App\Http\Controllers\Business\CustomerController;
use App\Http\Controllers\Business\SupplierController;
use App\Http\Controllers\Business\DashboardController;
use App\Http\Controllers\Business\DailyOutcomeController;
use App\Http\Controllers\Business\IncomingItemController;
use App\Http\Controllers\Business\AccountReceivableController;
use App\Http\Controllers\Business\BusinessBalanceActivityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/villages', function(){
    $villages = Village::where('nama', 'like', '%' . request('village') . '%')->skip(0)->take(5)->get();

    $data = [];

    foreach ($villages as $key => $village) {
        $district = District::where('kode', $village->kode_kecamatan)->first();
        $regency = Regency::where('kode', $district['kode_kabupaten'])->first();
        $province = Province::where('kode', $regency['kode_provinsi'])->first();
        $data[$key] = [
            'desa' => $village->nama,
            'kecamatan' => $district['nama'],
            'kabupaten' => $regency['nama'],
            'provinsi' => $province['nama'],            
        ];
    }

    $response = [
        'message' => 'Berhasil Mengambil Data Desa',
        'data' => $data,
    ];

    try {
        return response()->json($response, Response::HTTP_OK);
    } catch (\Throwable $th) {
        return response()->json($th, 500);
    }     

});

Route::get('/income/{income}', function(Income $income){
    
    $response = [
        'message' => 'Berhasil Mengambil Data Income',
        'data' => $income,
    ];

    try {
        return response()->json($response, Response::HTTP_OK);
    } catch (\Throwable $th) {
        return response()->json($th, 500);
    } 
});

Route::get('/outcome/{outcome}', function(Outcome $outcome){
    
    $response = [
        'message' => 'Berhasil Mengambil Data outcome',
        'data' => $outcome,
    ];

    try {
        return response()->json($response, Response::HTTP_OK);
    } catch (\Throwable $th) {
        return response()->json($th, 500);
    } 
});

Route::get('/business/{business}', function(Business $business){
    
    $response = [
        'message' => 'Berhasil Mengambil Data Unit Usaha',
        'data' => $business,
    ];

    try {
        return response()->json($response, Response::HTTP_OK);
    } catch (\Throwable $th) {
        return response()->json($th, 500);
    } 
});

Route::get('/{business}/category', [CategoryController::class, 'search']);
Route::post('/{business}/category', [CategoryController::class, 'apiValidate']);

Route::get('/{business}/brand', [BrandController::class, 'search']);
Route::post('/{business}/brand', [BrandController::class, 'apiValidate']);

Route::get('/{business}/supplier', [SupplierController::class, 'search']);
Route::get('/{business}/supplier/{supplier}', [SupplierController::class, 'detail']);
Route::post('/{business}/supplier', [SupplierController::class, 'apiValidate']);

Route::get('/{business}/customer/{customer}', [CustomerController::class, 'detail']);
Route::get('/{business}/customer', [CustomerController::class, 'search']);
Route::post('/{business}/customer', [CustomerController::class, 'apiValidate']);

Route::get('/{business}/product/{product}', [ProductController::class, 'detail']);
Route::get('/{business}/product', [ProductController::class, 'search']);
Route::get('/{business}/product-menu', [ProductController::class, 'searchMenu']);
Route::post('/{business}/product', [ProductController::class, 'apiValidate']);

Route::post('/{business}/incoming-item', [IncomingItemController::class, 'apiValidate']);

Route::get('/incoming-item/stock/{stock}', [IncomingItemController::class, 'getStock']);

Route::get('/{business}/stock/{stock}', [StockController::class, 'detail']);
Route::post('/{business}/stock', [StockController::class, 'apiValidate']);
Route::get('/stock/{product}', [StockController::class, 'search']);

Route::get('/asset/{asset}', [AssetController::class, 'detail']);
Route::post('/asset', [AssetController::class, 'apiValidate']);

Route::post('/{business}/cashier', [CashierController::class, 'store']);

Route::get('/invoice-detail/{invoice}', [CashierController::class, 'invoiceDetail']);
Route::delete('/invoice-detail/{invoiceId}/{productId}', [CashierController::class, 'deleteInvoiceDetail']);
Route::post('/cashier/add-order', [CashierController::class, 'addOrder']);
Route::post('/cashier/{invoice}/update', [CashierController::class, 'invoiceUpdate']);

Route::get('/{business}/account-receivable/{accountReceivable}', [AccountReceivableController::class, 'detail']);
Route::get('/{business}/pay-later', [AccountReceivableController::class, 'payLaterList']);
Route::get('/pay-later/detail/{id}', [AccountReceivableController::class, 'payLaterDetail']);

Route::get('/expense/{expense}', [DailyOutcomeController::class, 'detail']);
Route::post('/{business}/expense', [DailyOutcomeController::class, 'apiValidate']);

// dashboard
Route::get('/{business}/dashboard/cashflow', [DashboardController::class, 'cashflow']);
Route::post('/{business}/dashboard/business-balance-activity', [BusinessBalanceActivityController::class, 'apiValidate']);
Route::get('/{business}/dashboard/business-balance-activity/{businessBalanceActivity}', [BusinessBalanceActivityController::class, 'detail']);
