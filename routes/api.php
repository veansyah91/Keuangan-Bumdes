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
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\FixedAssetController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\CashMutationController;
use App\Http\Controllers\BalanceReportController;
use App\Http\Controllers\Business\AssetController;
use App\Http\Controllers\Business\BrandController;
use App\Http\Controllers\Business\StockController;
use App\Http\Controllers\CashflowReportController;
use App\Http\Controllers\Business\CashierController;
use App\Http\Controllers\Business\ProductController;
use App\Http\Controllers\LostProfitReportController;
use App\Http\Controllers\Business\CategoryController;
use App\Http\Controllers\Business\CustomerController;
use App\Http\Controllers\Business\SupplierController;
use App\Http\Controllers\Business\DashboardController;
use App\Http\Controllers\SubCategoryAccountController;
use App\Http\Controllers\TrialBalanceReportController;
use App\Http\Controllers\Business\DailyOutcomeController;
use App\Http\Controllers\Business\IncomingItemController;
use App\Http\Controllers\FixedAssetDepreciationController;
use App\Http\Controllers\Business\BusinessLedgerController;
use App\Http\Controllers\Business\BusinessAccountController;
use App\Http\Controllers\Business\BusinessJournalController;
use App\Http\Controllers\Business\AccountReceivableController;
use App\Http\Controllers\Business\BusinessBalanceActivityController;
use App\Http\Controllers\Business\BusinessBalanceElectricActivityController;

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

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::resource('/sub-category-account', SubCategoryAccountController::class)->only(['index', 'store', 'destroy']);

    Route::group(['middleware' => ['role:ADMIN']], function(){
        Route::get('/home/lost-profit',[AdminController::class, 'lostProfit']);
        Route::get('/home/asset',[AdminController::class, 'asset']);
        Route::get('/home/liability',[AdminController::class, 'liability']);
        Route::get('/home/equity',[AdminController::class, 'equity']);
    
        Route::get('/no-ref-contact-recomendation', [ContactController::class, 'noRefContactRecomendation']);
        Route::get('/contact', [ContactController::class, 'getApiData']);
        Route::get('/contacts', [ContactController::class, 'getData']);
        Route::resource('/contact', ContactController::class)->only(['store','destroy','show', 'update']);
        
        Route::get('/no-ref-fixed-asset-recomendation', [FixedAssetController::class, 'noRefFixedAssetRecomendation']);
        Route::get('/fixed-assets', [FixedAssetController::class, 'getData']);
        Route::resource('/fixed-asset', FixedAssetController::class)->only(['store','destroy','show', 'update','edit']);
    
        Route::get('/fixed-asset-depreciation', FixedAssetDepreciationController::class);
    
        Route::get('/account', [AccountController::class, 'getApiData']);
        Route::get('/account/{id}', [AccountController::class, 'show']);
        Route::put('/account/{id}', [AccountController::class, 'update']);
        Route::post('/account', [AccountController::class, 'store']);
        Route::get('/journal', [JournalController::class, 'getApiData']);
        Route::get('/no-ref-journal-recomendation', [JournalController::class, 'noRefJournalRecomendation']);
        Route::resource('/journal', JournalController::class)->only(['store','destroy','show', 'update']);
    
        Route::get('/no-ref-revenue-recomendation', [RevenueController::class, 'noRefRevenueRecomendation']);
        Route::get('/revenue', [RevenueController::class, 'getApiData']);
        Route::resource('/revenue', RevenueController::class)->only(['store','destroy','show', 'update']);
    
        Route::get('/no-ref-expense-recomendation', [ExpenseController::class, 'noRefExpenseRecomendation']);
        Route::get('/expense', [ExpenseController::class, 'getApiData']);
        Route::resource('/expense', ExpenseController::class)->only(['store','destroy','show', 'update']);
    
        Route::get('/no-ref-cash-mutation-recomendation', [CashMutationController::class, 'noRefCashMutationRecomendation']);
        Route::get('/cash-mutation', [CashMutationController::class, 'getApiData']);
        Route::resource('/cash-mutation', CashMutationController::class)->only(['store','destroy','show', 'update']);
    
        Route::get('/report/cashflow', [CashflowReportController::class, 'getApiData']);
        Route::get('/report/balance', [BalanceReportController::class, 'getApiData']);
        Route::get('/report/balance-year', [BalanceReportController::class, 'getApiDataYear']);
        Route::get('/report/lost-profit', [LostProfitReportController::class, 'getApiData']);
        Route::get('/report/lost-profit-year', [LostProfitReportController::class, 'getApiDataYear']);
        Route::get('/report/trial-balance', [TrialBalanceReportController::class, 'getApiData']);
    
        Route::get('/ledger',[LedgerController::class, 'getApiData']);
    });

    Route::group(['middleware' => ['role:OPERATOR']], function(){
        Route::get('/{business}/account', [BusinessAccountController::class, 'getApiData']);
        Route::get('/{business}/account/{businessaccount}', [BusinessAccountController::class, 'show']);
        Route::post('/{business}/account', [BusinessAccountController::class, 'store']);
        Route::put('/{business}/account/{businessaccount}', [BusinessAccountController::class, 'update']);

        Route::get('/{business}/journal', [BusinessJournalController::class, 'getApiData']);
        Route::get('/{business}/journal/{businessjournal}', [BusinessJournalController::class, 'show']);
        Route::post('/{business}/journal', [BusinessJournalController::class, 'store']);
        Route::put('/{business}/journal/{businessjournal}', [BusinessJournalController::class, 'update']);
        Route::get('/{business}/no-ref-journal-recomendation', [BusinessJournalController::class, 'noRefJournalRecomendation']);

        Route::get('/{business}/ledger', [BusinessLedgerController::class, 'getApiData']);

    });
    
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

Route::get('/{business}/balance-transaction', [BusinessBalanceElectricActivityController::class, 'indexApi']);

Route::get('/invoice-detail/{invoice}', [CashierController::class, 'invoiceDetail']);
Route::delete('/invoice-detail/{invoiceId}/{productId}', [CashierController::class, 'deleteInvoiceDetail']);
Route::post('/cashier/add-order', [CashierController::class, 'addOrder']);
Route::post('/cashier/{invoice}/update', [CashierController::class, 'invoiceUpdate']);

Route::get('/{business}/account-receivable/{accountReceivable}', [AccountReceivableController::class, 'detail']);
Route::get('/{business}/pay-later', [AccountReceivableController::class, 'payLaterList']);
Route::get('/pay-later/detail/{id}', [AccountReceivableController::class, 'payLaterDetail']);

// Route::get('/expense/{expense}', [DailyOutcomeController::class, 'detail']);
// Route::post('/{business}/expense', [DailyOutcomeController::class, 'apiValidate']);

// dashboard
Route::get('/{business}/dashboard/cashflow', [DashboardController::class, 'cashflow']);
Route::post('/{business}/dashboard/business-balance-activity', [BusinessBalanceActivityController::class, 'apiValidate']);
Route::get('/{business}/dashboard/business-balance-activity/{businessBalanceActivity}', [BusinessBalanceActivityController::class, 'detail']);
