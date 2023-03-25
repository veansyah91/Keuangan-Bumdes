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
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\FixedAssetController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\CashMutationController;
use App\Http\Controllers\BalanceReportController;
use App\Http\Controllers\Business\LendController;
use App\Http\Controllers\Business\AssetController;
use App\Http\Controllers\Business\BrandController;
use App\Http\Controllers\Business\StockController;
use App\Http\Controllers\CashflowReportController;
use App\Http\Controllers\Business\CashierController;
use App\Http\Controllers\Business\DepositController;
use App\Http\Controllers\Business\InvoiceController;
use App\Http\Controllers\Business\OverDueController;
use App\Http\Controllers\Business\ProductController;
use App\Http\Controllers\InvoiceSubscribeController;
use App\Http\Controllers\LostProfitReportController;
use App\Http\Controllers\Business\CategoryController;
use App\Http\Controllers\Business\CustomerController;
use App\Http\Controllers\Business\SupplierController;
use App\Http\Controllers\Business\DashboardController;
use App\Http\Controllers\SubCategoryAccountController;
use App\Http\Controllers\TrialBalanceReportController;
use App\Http\Controllers\Business\WithdrawalController;
use App\Http\Controllers\Business\StockOpnameController;
use App\Http\Controllers\Business\DailyOutcomeController;
use App\Http\Controllers\Business\IncomingItemController;
use App\Http\Controllers\Business\PurchaseGoodsController;
use App\Http\Controllers\Business\SavingAccountController;
use App\Http\Controllers\FixedAssetDepreciationController;
use App\Http\Controllers\Business\AccountPayableController;
use App\Http\Controllers\Business\BusinessLedgerController;
use App\Http\Controllers\Business\DebtSubmissionController;
use App\Http\Controllers\Business\BusinessAccountController;
use App\Http\Controllers\Business\BusinessExpenseController;
use App\Http\Controllers\Business\BusinessJournalController;
use App\Http\Controllers\Business\BusinessRevenueController;
use App\Http\Controllers\Business\ChangesInEquityController;
use App\Http\Controllers\Business\AccountReceivableController;
use App\Http\Controllers\Business\CreditApplicationController;
use App\Http\Controllers\Business\BusinessFixedAssetController;
use App\Http\Controllers\Business\CreditSalesInvoiceController;
use App\Http\Controllers\Business\ProductStockOpnameController;
use App\Http\Controllers\Business\InventoryAdjustmentController;
use App\Http\Controllers\Business\BusinessCashMutationController;
use App\Http\Controllers\Business\AccountPayablePaymentController;
use App\Http\Controllers\Business\BusinessBalanceReportController;
use App\Http\Controllers\Business\BusinessCashflowReportController;
use App\Http\Controllers\Business\BusinessBalanceActivityController;
use App\Http\Controllers\Business\AccountReceivablePaymentController;
use App\Http\Controllers\Business\BusinessLostProfitReportController;
use App\Http\Controllers\Business\BusinessTrialBalanceReportController;
use App\Http\Controllers\Business\BusinessFixedAssetDepreciationController;
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
    Route::get('/contact', [ContactController::class, 'getApiData']);
    Route::get('/contacts', [ContactController::class, 'getData']);
    Route::get('/contact-with-detail', [ContactController::class, 'detail']);
    Route::resource('/contact', ContactController::class)->only(['store','destroy','show', 'update']);
    Route::get('/no-ref-contact-recomendation', [ContactController::class, 'noRefContactRecomendation']);

    //Regional
    //village
    Route::get('/village', function(){
        $villages = Village::paginate(50);

        return response()->json([
            'status' => 'success',
            'data' => $villages,
        ]);
    });

    Route::group(['middleware' => ['auth']], function(){
        Route::get('/invoice-subscribe/{id}/payment-confirmation', [InvoiceSubscribeController::class, 'paymentConfirmation'])->name('invoice.subscribe.payment-confirmation')->middleware('admin');

    });

    Route::group(['middleware' => ['role:ADMIN']], function(){
        //dasbor
            Route::get('/home/lost-profit',[AdminController::class, 'lostProfit']);
            Route::get('/home/asset',[AdminController::class, 'asset']);
            Route::get('/home/liability',[AdminController::class, 'liability']);
            Route::get('/home/equity',[AdminController::class, 'equity']);
        //

        //
            Route::get('/business/{business}', [BusinessController::class, 'show']);
        //
        
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
        Route::get('/report/changes-in-equity', [App\Http\Controllers\ChangesInEquityController::class, 'getApiData']);
        Route::get('/report/trial-balance', [TrialBalanceReportController::class, 'getApiData']);
    
        Route::get('/ledger',[LedgerController::class, 'getApiData']);
    });

        //fixed asset
            Route::get('/{business}/no-ref-fixed-asset-recomendation', [BusinessFixedAssetController::class, 'noRefFixedAssetRecomendation']);
            Route::get('/{business}/fixed-assets', [BusinessFixedAssetController::class, 'getData']);
            Route::post('/{business}/fixed-asset', [BusinessFixedAssetController::class, 'store']);
            Route::get('/{business}/fixed-asset/{businessfixedasset}',[BusinessFixedAssetController::class, 'show']);
            Route::get('/{business}/fixed-asset/{businessfixedasset}/edit',[BusinessFixedAssetController::class, 'edit']);
            Route::put('/{business}/fixed-asset/{businessfixedasset}', [BusinessFixedAssetController::class, 'update']);
            Route::delete('/{business}/fixed-asset/{businessfixedasset}', [BusinessFixedAssetController::class, 'destroy']);
        
            Route::get('/{business}/fixed-asset-depreciation', BusinessFixedAssetDepreciationController::class);
        //

        //saving-account
            Route::get('/{business}/no-ref-saving-account-recomendation', [SavingAccountController::class, 'noRefSavingAccountRecomendation']);
            Route::get('/{business}/saving-account', [SavingAccountController::class, 'getData']);
            Route::get('/{business}/saving-account/{id}/book-api', [SavingAccountController::class, 'getDataAccountPayable']);
            Route::post('/{business}/saving-contact', [SavingAccountController::class, 'store']);
            Route::get('/{business}/saving-contact/{id}', [SavingAccountController::class, 'show']);
            Route::put('/{business}/saving-contact/{id}', [SavingAccountController::class, 'update']);
            Route::delete('/{business}/saving-contact/{id}', [SavingAccountController::class, 'destroy']);
        //

        //deposit
            Route::get('/{business}/no-ref-deposit-recomendation', [DepositController::class, 'noRefDepositRecomendation']);
            Route::get('/{business}/deposit', [DepositController::class, 'getData']);
            Route::post('/{business}/deposit', [DepositController::class, 'store']);
            Route::get('/{business}/deposit/{id}', [DepositController::class, 'show']);
            Route::put('/{business}/deposit/{id}', [DepositController::class, 'update']);
            Route::delete('/{business}/deposit/{id}', [DepositController::class, 'destroy']);
        //

        //withdrawal
            Route::get('/{business}/no-ref-withdrawal-recomendation', [WithdrawalController::class, 'noRefWithdrawalRecomendation']);
            Route::get('/{business}/withdrawal', [WithdrawalController::class, 'getData']);
            Route::post('/{business}/withdrawal', [WithdrawalController::class, 'store']);
            Route::get('/{business}/withdrawal/{id}', [WithdrawalController::class, 'show']);
            Route::put('/{business}/withdrawal/{id}', [WithdrawalController::class, 'update']);
            Route::delete('/{business}/withdrawal/{id}', [WithdrawalController::class, 'destroy']);
        //

        //debt submission
            Route::get('/{business}/no-ref-debt-submission-recomendation', [DebtSubmissionController::class, 'noRefDebtSubmissionRecomendation']);
            Route::get('/{business}/debt-submission', [DebtSubmissionController::class, 'getData']);
            Route::post('/{business}/debt-submission', [DebtSubmissionController::class, 'store']);
            Route::get('/{business}/debt-submission/{id}', [DebtSubmissionController::class, 'show']);
            Route::put('/{business}/debt-submission/{id}', [DebtSubmissionController::class, 'update']);
            Route::delete('/{business}/debt-submission/{id}', [DebtSubmissionController::class, 'destroy']);
        //

        //lend
            Route::get('/{business}/no-ref-lend-recomendation', [LendController::class, 'noRefLendRecomendation']);
            Route::get('/{business}/lend', [LendController::class, 'getData']);
            Route::post('/{business}/lend', [LendController::class, 'store']);
            Route::get('/{business}/lend/{id}', [LendController::class, 'show']);
            Route::put('/{business}/lend/{id}', [LendController::class, 'update']);
            Route::delete('/{business}/lend/{id}', [LendController::class, 'destroy']);
        //

        //credit application
            Route::get('/{business}/no-ref-credit-application-recomendation', [CreditApplicationController::class, 'noRefCreditApplicationRecomendation']);
            Route::get('/{business}/credit-application', [CreditApplicationController::class, 'getData']);
            Route::post('/{business}/credit-application', [CreditApplicationController::class, 'store']);
            Route::get('/{business}/credit-application/{id}', [CreditApplicationController::class, 'show']);
            Route::put('/{business}/credit-application/{id}', [CreditApplicationController::class, 'update']);
            Route::delete('/{business}/credit-application/{id}', [CreditApplicationController::class, 'destroy']);
        //

        //credit-sales
            Route::get('/{business}/no-ref-credit-sales-recomendation', [CreditSalesInvoiceController::class, 'noRefCreditSalesInvoiceRecomendation']);
            Route::get('/{business}/credit-sales', [CreditSalesInvoiceController::class, 'getData']);
            Route::post('/{business}/credit-sales', [CreditSalesInvoiceController::class, 'store']);
            Route::get('/{business}/credit-sales/{id}', [CreditSalesInvoiceController::class, 'show']);
            Route::put('/{business}/credit-sales/{id}', [CreditSalesInvoiceController::class, 'update']);
            Route::delete('/{business}/credit-sales/{id}', [CreditSalesInvoiceController::class, 'destroy']);
        //

        //category
            Route::get('/{business}/category', [CategoryController::class, 'search']);
            Route::post('/{business}/category', [CategoryController::class, 'store']);
            Route::delete('/{business}/category/{category}', [CategoryController::class, 'destroy']);
        //

        //product
            Route::get('/{business}/no-ref-product-recomendation', [ProductController::class, 'noRefProductRecomendation']);
            Route::get('/{business}/product/{product}', [ProductController::class, 'detail']);
            Route::delete('/{business}/product/{product}', [ProductController::class, 'destroy']);
            Route::put('/{business}/product/{product}', [ProductController::class, 'update']);
            Route::get('/{business}/product', [ProductController::class, 'search']);
            Route::post('/{business}/product', [ProductController::class, 'store']);
        // 

        //Produk for Stock Opname
            Route::get('/{business}/product-stock-opname', ProductStockOpnameController::class);
        //

        //inventory adjustment
            Route::get('/{business}/no-ref-inventory-adjustment-recomendation', [InventoryAdjustmentController::class, 'noRefInventoryAdjustmentRecomendation']);
            Route::get('/{business}/inventory-adjustment', [InventoryAdjustmentController::class, 'getData']);
            Route::get('/{business}/inventory-adjustment/{inventoryadjustment}', [InventoryAdjustmentController::class, 'show']);
            Route::put('/{business}/inventory-adjustment/{inventoryadjustment}', [InventoryAdjustmentController::class, 'update']);
            Route::post('/{business}/inventory-adjustment', [InventoryAdjustmentController::class, 'store']);
            Route::delete('/{business}/inventory-adjustment/{inventoryadjustment}', [InventoryAdjustmentController::class, 'destroy']);
        //

        //stock opname
            Route::get('/{business}/no-ref-stock-opname-recomendation', [StockOpnameController::class, 'noRefStockOpnameRecomendation']);
            Route::get('/{business}/stock-opname', [StockOpnameController::class, 'getData']);
            Route::get('/{business}/stock-opname/{stockOpname}', [StockOpnameController::class, 'show']);
            Route::put('/{business}/stock-opname/{stockOpname}', [StockOpnameController::class, 'update']);
            Route::post('/{business}/stock-opname', [StockOpnameController::class, 'store']);
            Route::delete('/{business}/stock-opname/{stockOpname}', [StockOpnameController::class, 'destroy']);
        //

        //invoice
            Route::get('/{business}/no-ref-invoice-recomendation', [InvoiceController::class, 'noRefInvoiceRecomendation']);
            Route::get('/{business}/invoice', [InvoiceController::class, 'getData']);
            Route::get('/{business}/invoice/{id}', [InvoiceController::class, 'show']);
            Route::put('/{business}/invoice/{id}', [InvoiceController::class, 'update']);
            Route::post('/{business}/invoice', [InvoiceController::class, 'store']);
            Route::delete('/{business}/invoice/{invoice}', [InvoiceController::class, 'destroy']);
        //
    
        //account receivable
            Route::get('/{business}/no-ref-account-receivable-recomendation', [AccountReceivableController::class, 'noRefAccountReceivableRecomendation']);
            Route::get('/{business}/account-receivable', [AccountReceivableController::class, 'getData']);
            Route::get('/{business}/account-receivable-by-invoice/{contact}', [AccountReceivableController::class, 'getDataByInvoice']);
            Route::get('/{business}/account-receivable/{id}', [AccountReceivableController::class, 'show']);
            Route::get('/{business}/account-receivable/{id}/lend', [AccountReceivableController::class, 'lend']);
            Route::put('/{business}/account-receivable/{id}', [AccountReceivableController::class, 'update']);
            Route::post('/{business}/account-receivable', [AccountReceivableController::class, 'store']);
            Route::delete('/{business}/account-receivable/{account-receivable}', [AccountReceivableController::class, 'destroy']);
        //

        //account receivable payment
            Route::get('/{business}/no-ref-account-receivable-payment-recomendation', [AccountReceivablePaymentController::class, 'noRefAccountReceivablePaymentRecomendation']);
            Route::get('/{business}/account-receivable-payment', [AccountReceivablePaymentController::class, 'getApi']);
            Route::post('/{business}/account-receivable-payment', [AccountReceivablePaymentController::class, 'store']);
            Route::get('/{business}/account-receivable-payment/{id}', [AccountReceivablePaymentController::class, 'show']);
            Route::put('/{business}/account-receivable-payment/{id}', [AccountReceivablePaymentController::class, 'update']);
            Route::delete('/{business}/account-receivable-payment/{id}', [AccountReceivablePaymentController::class, 'destroy']);
        //

        //over due
            Route::get('/{business}/over-due', [OverDueController::class, 'getApi']);
            Route::get('/{business}/over-due/{id}', [OverDueController::class, 'getSingleApi']);
            Route::delete('/{business}/over-due/{id}', [OverDueController::class, 'writeOff']);
            Route::get('/{business}/over-due/reload', [OverDueController::class, 'update']);
        //

        //purchase goods
            Route::get('/{business}/no-ref-purchase-goods-recomendation', [PurchaseGoodsController::class, 'noRefPurchaseGoodsRecomendation']);
            Route::get('/{business}/purchase-goods', [PurchaseGoodsController::class, 'getData']);
            Route::get('/{business}/purchase-goods/{id}', [PurchaseGoodsController::class, 'show']);
            Route::put('/{business}/purchase-goods/{id}', [PurchaseGoodsController::class, 'update']);
            Route::post('/{business}/purchase-goods', [PurchaseGoodsController::class, 'store']);
            Route::delete('/{business}/purchase-goods/{id}', [PurchaseGoodsController::class, 'destroy']);
        //

        //account payable
            Route::get('/{business}/no-ref-account-payable-recomendation', [AccountPayableController::class, 'noRefAccountPayableRecomendation']);
            Route::get('/{business}/account-payable', [AccountPayableController::class, 'getData']);
            Route::get('/{business}/account-payable-by-invoice/{contact}', [AccountPayableController::class, 'getDataByPurchaseGoods']);
            Route::get('/{business}/account-payable/{id}', [AccountPayableController::class, 'show']);
            Route::put('/{business}/account-payable/{id}', [AccountPayableController::class, 'update']);
            Route::post('/{business}/account-payable', [AccountPayableController::class, 'store']);
            Route::delete('/{business}/account-payable/{account-payable}', [AccountPayableController::class, 'destroy']);
        //

        //account payable payment
            Route::get('/{business}/no-ref-account-payable-payment-recomendation', [AccountPayablePaymentController::class, 'noRefAccountPayablePaymentRecomendation']);
            Route::get('/{business}/account-payable-payment', [AccountPayablePaymentController::class, 'getApi']);
            Route::post('/{business}/account-payable-payment', [AccountPayablePaymentController::class, 'store']);
            Route::get('/{business}/account-payable-payment/{id}', [AccountPayablePaymentController::class, 'show']);
            Route::put('/{business}/account-payable-payment/{id}', [AccountPayablePaymentController::class, 'update']);
            Route::delete('/{business}/account-payable-payment/{id}', [AccountPayablePaymentController::class, 'destroy']);
        //

        //Revenue
            Route::get('/{business}/no-ref-revenue-recomendation', [BusinessRevenueController::class, 'noRefRevenueRecomendation']);
            Route::get('/{business}/revenue', [BusinessRevenueController::class, 'getApiData']);
            Route::get('/{business}/revenue/{businessrevenue}', [BusinessRevenueController::class, 'show']);
            Route::post('/{business}/revenue', [BusinessRevenueController::class, 'store']);
            Route::put('/{business}/revenue/{businessrevenue}', [BusinessRevenueController::class, 'update']);
            Route::delete('/{business}/revenue/{businessrevenue}', [BusinessRevenueController::class, 'destroy']);
        //

        //expense
            Route::get('/{business}/no-ref-expense-recomendation', [BusinessExpenseController::class, 'noRefexpenseRecomendation']);
            Route::get('/{business}/expense', [BusinessExpenseController::class, 'getApiData']);
            Route::get('/{business}/expense/{businessexpense}', [BusinessExpenseController::class, 'show']);
            Route::post('/{business}/expense', [BusinessExpenseController::class, 'store']);
            Route::put('/{business}/expense/{businessexpense}', [BusinessExpenseController::class, 'update']);
            Route::delete('/{business}/expense/{businessexpense}', [BusinessExpenseController::class, 'destroy']);
        //

        //cash mutation
            Route::get('/{business}/no-ref-cash-mutation-recomendation', [BusinessCashMutationController::class, 'noRefCashMutationRecomendation']);
            Route::get('/{business}/cash-mutation', [BusinessCashMutationController::class, 'getApiData']);
            Route::get('/{business}/cash-mutation/{businesscashmutation}', [BusinessCashMutationController::class, 'show']);
            Route::post('/{business}/cash-mutation', [BusinessCashMutationController::class, 'store']);
            Route::put('/{business}/cash-mutation/{businesscashmutation}', [BusinessCashMutationController::class, 'update']);
            Route::delete('/{business}/cash-mutation/{businesscashmutation}', [BusinessCashMutationController::class, 'destroy']);
        //

        //component dashboard
            Route::get('/{business}/home/lost-profit',[DashboardController::class, 'lostProfit']);
            Route::get('/{business}/home/asset',[DashboardController::class, 'asset']);
            Route::get('/{business}/home/liability',[DashboardController::class, 'liability']);
            Route::get('/{business}/home/equity',[DashboardController::class, 'equity']);

            //daily sales chart
                Route::get('/{business}/home/sales-chart',[DashboardController::class, 'salesChart']);
            //
        //

        //account
            Route::get('/{business}/account', [BusinessAccountController::class, 'getApiData']);
            Route::get('/{business}/account/{businessaccount}', [BusinessAccountController::class, 'show']);
            Route::post('/{business}/account', [BusinessAccountController::class, 'store']);
            Route::put('/{business}/account/{businessaccount}', [BusinessAccountController::class, 'update']);
        //

        //journal
            Route::get('/{business}/journal', [BusinessJournalController::class, 'getApiData']);
            Route::get('/{business}/journal/{businessjournal}', [BusinessJournalController::class, 'show']);
            Route::post('/{business}/journal', [BusinessJournalController::class, 'store']);
            Route::put('/{business}/journal/{businessjournal}', [BusinessJournalController::class, 'update']);
            Route::delete('/{business}/journal/{businessjournal}', [BusinessJournalController::class, 'destroy']);
            Route::get('/{business}/no-ref-journal-recomendation', [BusinessJournalController::class, 'noRefJournalRecomendation']);
        //

        //route
            Route::get('/{business}/ledger', [BusinessLedgerController::class, 'getApiData']);
        //

        //report
            Route::get('/{business}/report/cashflow', [BusinessCashflowReportController::class, 'getApiData']);
            Route::get('/{business}/report/balance', [BusinessBalanceReportController::class, 'getApiData']);
            Route::get('/{business}/report/balance-year', [BusinessBalanceReportController::class, 'getApiDataYear']);
            Route::get('/{business}/report/lost-profit', [BusinessLostProfitReportController::class, 'getApiData']);
            Route::get('/{business}/report/lost-profit-year', [BusinessLostProfitReportController::class, 'getApiDataYear']);
            Route::get('/{business}/report/changes-in-equity', [ChangesInEquityController::class, 'getApiData']);
            Route::get('/{business}/report/trial-balance', [BusinessTrialBalanceReportController::class, 'getApiData']);
        //
    
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

