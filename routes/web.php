<?php

use App\Models\Asset;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\Outcome;
use App\Models\Product;
use App\Models\Regency;

use App\Models\Village;
use App\Models\Business;
use App\Models\District;
use App\Models\Identity;
use App\Models\Province;
use App\Exports\AssetExport;
use App\Exports\StockExport;
use App\Helpers\MonthHelper;
use App\Models\BusinessUser;
use Illuminate\Http\Request;

use App\Exports\IncomeExport;
use App\Exports\OutcomeExport;
use App\Imports\RegencyImport;
use App\Imports\VillageImport;
use App\Imports\DistrictImport;
use App\Imports\ProvinceImport;
use App\Models\BusinessExpense;
// use Illuminate\Support\Facades\Auth;
use App\Helpers\BusinessUserHelper;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BusinessIncomeExport;
use Illuminate\Support\Facades\Route;
use App\Exports\BusinessExpenseExport;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\OutcomeController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\IdentityController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FixedAssetController;
use App\Http\Controllers\Auth\LogoutController;
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
use App\Http\Controllers\TrialBalanceReportController;
use App\Http\Controllers\Business\WithdrawalController;
use App\Http\Controllers\Business\DailyIncomeController;
use App\Http\Controllers\Business\StockOpnameController;
use App\Http\Controllers\Business\DailyOutcomeController;
use App\Http\Controllers\Business\IncomingItemController;
use App\Http\Controllers\Business\PurchaseGoodsController;
use App\Http\Controllers\Business\SavingAccountController;
use App\Http\Controllers\Business\AccountPayableController;
use App\Http\Controllers\Business\BusinessIncomeController;
use App\Http\Controllers\Business\BusinessLedgerController;
use App\Http\Controllers\Business\DebtSubmissionController;
use App\Http\Controllers\Business\BusinessAccountController;
use App\Http\Controllers\Business\BusinessContactController;
use App\Http\Controllers\Business\BusinessExpenseController;
use App\Http\Controllers\Business\BusinessJournalController;
use App\Http\Controllers\Business\BusinessRevenueController;
use App\Http\Controllers\Business\ChangesInEquityController;
use App\Http\Controllers\Business\AccountReceivableController;
use App\Http\Controllers\Business\CreditApplicationController;
use App\Http\Controllers\Business\BusinessFixedAssetController;
use App\Http\Controllers\Business\CreditSalesInvoiceController;
use App\Http\Controllers\Business\InventoryAdjustmentController;
use App\Http\Controllers\Business\BusinessCashMutationController;
use App\Http\Controllers\Business\AccountPayablePaymentController;
use App\Http\Controllers\Business\BusinessBalanceReportController;
use App\Http\Controllers\Business\BusinessCashflowReportController;
use App\Http\Controllers\Business\BusinessBalanceActivityController;
use App\Http\Controllers\Business\AccountReceivablePaymentController;
use App\Http\Controllers\Business\BusinessLostProfitReportController;
use App\Http\Controllers\Business\BusinessTrialBalanceReportController;
use App\Http\Controllers\Business\BusinessBalanceElectricActivityController;

Route::get('/', function () {
    $user = Auth::user();
    if (!$user) {
        return redirect('login');
    }
    if (Auth::user()->hasRole('DEV') || Auth::user()->hasRole('ADMIN')) {
        if (Auth::user()->hasRole('DEV')) {
            return redirect()->route('users.index');
        }
        return redirect()->route('admin.dashboard');
    }
    $businessUser = BusinessUser::where('user_id', Auth::user()->id)->first();

    return redirect()->route('business.dashboard', [
        'business' => $businessUser['business_id']
    ]);
});

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login');
Route::get('/over-due', [App\Http\Controllers\OverDueController::class, 'index'])->name('subscribe.overdue');
Route::get('/{business}/over-due-subscribe', [App\Http\Controllers\OverDueController::class, 'business'])->name('over.due.business');

Route::group(['middleware' => ['auth']], function(){
    
    Route::get('/invoice-subscribe', [InvoiceSubscribeController::class, 'index'])->name('invoice.subscribe.index')->middleware('admin');
    Route::post('/invoice-subscribe', [InvoiceSubscribeController::class, 'store'])->name('invoice.subscribe.store')->middleware('admin');
    Route::get('/invoice-subscribe/create', [InvoiceSubscribeController::class, 'create'])->name('invoice.subscribe.create')->middleware('admin');
    Route::get('/invoice-subscribe/{id}', [InvoiceSubscribeController::class, 'detail'])->name('invoice.subscribe.detail')->middleware('admin');
    Route::get('/invoice-subscribe/{id}/print-invoice', [InvoiceSubscribeController::class, 'print'])->name('invoice.subscribe.print')->middleware('admin');
    Route::put('/invoice-subscribe/{id}/confirm', [InvoiceSubscribeController::class, 'confirm'])->name('invoice.subscribe.confirm')->middleware('dev');
});

Route::group(['middleware' => ['auth', 'subscribe']], function(){
    Route::post('/logout', LogoutController::class)->name('logout');
    
    Route::group(['middleware' => ['admin']], function(){
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
        
        Route::resource('/identity', IdentityController::class)->middleware('admin');

        Route::resource('/contact', ContactController::class)->only(['index','create','edit'])->middleware('admin');
        Route::resource('/fixed-asset', FixedAssetController::class)->only(['index','create','edit'])->middleware('admin');

        Route::resource('/account', AccountController::class)->only(['index'])->middleware('admin');

        Route::resource('/journal', JournalController::class)->only(['index', 'create', 'edit'])->middleware('admin');
        Route::get('/journal/print-detail/{id}', [JournalController::class, 'printDetail']);
        Route::get('/journal/print', [JournalController::class, 'print']);

        Route::resource('/ledger', LedgerController::class)->only(['index'])->middleware('admin');
        Route::get('/ledger/print', [LedgerController::class, 'print']);

        Route::resource('/revenue', RevenueController::class)->only(['index','create','edit'])->middleware('admin');
        Route::get('/revenue/print-detail/{id}', [RevenueController::class, 'printDetail']);
        Route::get('/revenue/print', [RevenueController::class, 'print']);

        Route::resource('/expense', ExpenseController::class)->only(['index','create','edit'])->middleware('admin');
        Route::get('/expense/print-detail/{id}', [ExpenseController::class, 'printDetail']);
        Route::get('/expense/print', [ExpenseController::class, 'print']);

        Route::resource('/cash-mutation', CashMutationController::class)->only(['index','create','edit'])->middleware('admin');
        Route::get('/cash-mutation/print-detail/{id}', [CashMutationController::class, 'printDetail']);
        Route::get('/cash-mutation/print', [CashMutationController::class, 'print']);

        Route::get('/report/cashflow', [CashflowReportController::class, 'index'])->name('report.cashflow.index');
        Route::get('/report/cashflow-year', [CashflowReportController::class, 'year'])->name('report.cashflow.year');
        Route::get('/report/print-cashflow', [CashflowReportController::class, 'print'])->name('report.cashflow.print');
        Route::get('/report/print-cashflow-year', [CashflowReportController::class, 'printYear'])->name('report.cashflow.print.year');

        Route::get('/report/balance', [BalanceReportController::class, 'index'])->name('report.balance.index');
        Route::get('/report/print-balance', [BalanceReportController::class, 'print'])->name('report.balance.print');
        Route::get('/report/balance-year', [BalanceReportController::class, 'year'])->name('report.balance.year');
        Route::get('/report/print-balance-year', [BalanceReportController::class, 'printYear'])->name('report.balance.print.year');

        Route::get('/report/lost-profit', [LostProfitReportController::class, 'index'])->name('report.lost-profit.index');
        Route::get('/report/print-lost-profit', [LostProfitReportController::class, 'print'])->name('report.lost-profit.print');
        Route::get('/report/lost-profit-year', [LostProfitReportController::class, 'year'])->name('report.lost-profit-year.index');
        Route::get('/report/lost-profit-year-print', [LostProfitReportController::class, 'printYear'])->name('report.lost-profit-year.print');

        Route::get('/report/changes-in-equity', [App\Http\Controllers\ChangesInEquityController::class, 'index'])->name('report.changes-in-equity.index');
        Route::get('/report/changes-in-equity-print', [App\Http\Controllers\ChangesInEquityController::class, 'print'])->name('report.changes-in-equity.print');


        Route::get('/report/trial-balance', [TrialBalanceReportController::class, 'index'])->name('report.trial-balance.index');
        Route::get('/report/trial-balance-print', [TrialBalanceReportController::class, 'print'])->name('report.trial-balance.print');

        Route::resource('/income', IncomeController::class);
    
        Route::resource('/outcome', OutcomeController::class);
    
        Route::resource('/business', BusinessController::class);
    
        Route::get('/income-pdf', function(Request $request){
            $tanggal_awal = $request['tanggal_awal'];
            $tanggal_akhir = $request['tanggal_akhir'];
    
            $identity = Identity::first();
    
            $incomes = ($tanggal_awal && $tanggal_akhir) ? 
                          Income::whereBetween('tanggal_masuk', [$tanggal_awal, $tanggal_akhir])->orderBy('tanggal_masuk', 'asc')->get()
                        : Income::orderBy('tanggal_masuk', 'asc')->get();
    
                        try {
                            $pdf = PDF::loadview('report.report-income', [
                                                                            'incomes' => $incomes,
                                                                            'identity' => $identity,
                                                                            'total' => $incomes->sum('jumlah')
                                                                        ]);
                            return $pdf->download('report-income.pdf');
                        } catch (\Throwable $th) {
                            abort(403, 'Data Terlalu Besar');
                        }
    
            
        });
    
        Route::get('/income-excel', function(Request $request){
            $tanggal_awal = $request['tanggal_awal'];
            $tanggal_akhir = $request['tanggal_akhir'];
    
            try {                        
                return (new IncomeExport($tanggal_awal, $tanggal_akhir))->download('incomes.xlsx');
            } catch (\Throwable $th) {
                abort(503, 'Terjadi Kesalahan');
            }
    
            
        });
    
        Route::get('/outcome-excel', function(Request $request){
            $tanggal_awal = $request['tanggal_awal'];
            $tanggal_akhir = $request['tanggal_akhir'];
    
            try {                        
                return (new OutcomeExport($tanggal_awal, $tanggal_akhir))->download('outcomes.xlsx');
            } catch (\Throwable $th) {
                abort(503, 'Terjadi Kesalahan');
            }    
            
        });
    
        Route::get('/outcome-pdf', function(Request $request){
            $tanggal_awal = $request['tanggal_awal'];
            $tanggal_akhir = $request['tanggal_akhir'];
    
            $identity = Identity::first();
    
            $outcomes = ($tanggal_awal && $tanggal_akhir) ? 
                          Outcome::whereBetween('tanggal_keluar', [$tanggal_awal, $tanggal_akhir])->orderBy('tanggal_keluar', 'asc')->get()
                        : Outcome::orderBy('tanggal_keluar', 'asc')->get();
    
            try {
                $pdf = PDF::loadview('report.report-outcome', [
                                                                'outcomes' => $outcomes,
                                                                'identity' => $identity,
                                                                'total' => $outcomes->sum('jumlah')
                                                            ]);
                return $pdf->download('report-outcome.pdf');
            } catch (\Throwable $th) {
                abort(503, 'Terjadi Kesalahan');
            }
    
            
        });
    
        //roles 
            Route::get('/roles', [RolesController::class, 'index'])->name('roles.index');
            Route::post('/roles', [RolesController::class, 'store'])->name('roles.store');
            Route::delete('/roles/{role}', [RolesController::class, 'destroy'])->name('roles.destroy');
        //    
    
        // import data wilayah
            Route::get('/import-asset', function(){
                return view('admin.import-asset', [
                    'provinsi' => Province::first(),
                    'kabupaten' => Regency::first(),
                    'kecamatan' => District::first(),
                    'desa' => Village::first()
                ]);
            })->name('import-asset');
            
            Route::post('/import-asset/village', function(Request $request){
                $validatedData = $request->validate([
                    'desaFile' => 'required'
                ]);
    
                $file = $request->file('desaFile');
    
                try {
                    Excel::import(new VillageImport, $file);
                } catch (\Throwable $th) {
                    abort(503, 'Terjadi Kesalahan');
                }
    
                return redirect('/import-asset');
            })->name('import-asset.village');
    
            Route::post('/import-asset/district', function(Request $request){
                $validatedData = $request->validate([
                    'kecamatanFile' => 'required'
                ]);
    
                $file = $request->file('kecamatanFile');
    
                try {
                    Excel::import(new DistrictImport, $file);
                } catch (\Throwable $th) {
                    abort(503, 'Terjadi Kesalahan');
                }
    
                return redirect('/import-asset');
            })->name('import-asset.district');
    
            Route::post('/import-asset/regency', function(Request $request){
                $validatedData = $request->validate([
                    'kabupatenFile' => 'required'
                ]);
    
                $file = $request->file('kabupatenFile');
    
                try {
                    Excel::import(new RegencyImport, $file);
                } catch (\Throwable $th) {
                    abort(503, 'Terjadi Kesalahan');
                }
    
                return redirect('/import-asset');
            })->name('import-asset.regency');
    
            Route::post('/import-asset/province', function(Request $request){
                $validatedData = $request->validate([
                    'provinsiFile' => 'required'
                ]);
    
                $file = $request->file('provinsiFile');
    
                try {
                    Excel::import(new ProvinceImport, $file);
                } catch (\Throwable $th) {
                    abort(503, 'Terjadi Kesalahan');
                }
    
                return redirect('/import-asset');
            })->name('import-asset.province');
        // 
    
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [UserController::class, 'delete'])->name('users.delete');
        Route::patch('/users/reset-password/{user}', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::patch('/users/reset-role/{user}', [UserController::class, 'resetRole'])->name('users.reset-role');
    });

    Route::get('/users/change-password', [UserController::class, 'changePassword'])->name('users.change-password');
    Route::patch('/users/change-password', [UserController::class, 'updatePassword'])->name('users.change-password.store');

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Route Business 
        // Dashboard Page 
            Route::get('/{business}/dashboard', [DashboardController::class, 'index'])->name('business.dashboard');
            Route::patch('/{business}/dashboard/update-business-balance', [DashboardController::class, 'updateBusinessBalance'])->name('business.dashboard.update-business-balance');

            Route::get('/{business}/dashboard/business-balance-activity', [BusinessBalanceActivityController::class, 'index'])->name('business.business-balance-activity.index');
            Route::post('/{business}/dashboard/business-balance-activity', [BusinessBalanceActivityController::class, 'store'])->name('business.business-balance-activity.store');
            Route::patch('/{business}/dashboard/business-balance-activity/{businessBalanceActivity}', [BusinessBalanceActivityController::class, 'update'])->name('business.business-balance-activity.update');
            Route::delete('/{business}/dashboard/business-balance-activity/{businessBalanceActivity}', [BusinessBalanceActivityController::class, 'delete'])->name('business.business-balance-activity.delete');
        // 

        //master
            // contact
                Route::get('/{business}/contact', [BusinessContactController::class, 'index'])->name('business.contact.index');
            //
            // fixed-asset
                Route::get('/{business}/fixed-asset', [BusinessFixedAssetController::class, 'index'])->name('business.fixed-asset.index');
            //
        //

        //saving
            Route::get('/{business}/saving-account', [SavingAccountController::class, 'index'])->name('business.saving-account.index');
            Route::get('/{business}/saving-account/print', [SavingAccountController::class, 'print'])->name('business.saving-account.print');
            Route::get('/{business}/saving-account/{id}/book', [SavingAccountController::class, 'book'])->name('business.saving-account.book');
        //

        //deposit
            Route::get('/{business}/deposit', [DepositController::class, 'index'])->name('business.deposit.index');
            Route::get('/{business}/deposit/{id}/print-detail', [DepositController::class, 'printDetail']);
        //

        //debt submission
            Route::get('/{business}/debt-submission', [DebtSubmissionController::class, 'index'])->name('business.debt-submission.index');
        //

        //lend
            Route::get('/{business}/lend', [LendController::class, 'index'])->name('business.lend.index');
            Route::get('/{business}/lend/{id}/card', [LendController::class, 'card'])->name('business.lend.card');
        //

        //credit application
            Route::get('/{business}/credit-application', [CreditApplicationController::class, 'index'])->name('business.credit-application.index');
        //

        //credit sales
            Route::get('/{business}/credit-sales', [CreditSalesInvoiceController::class, 'index'])->name('business.credit-sales.index');
            Route::get('/{business}/credit-sales/{id}/print-detail', [CreditSalesInvoiceController::class, 'printDetail'])->name('business.credit-sales.print-detail');
            Route::get('/{business}/credit-sales/{id}/card', [CreditSalesInvoiceController::class, 'card'])->name('business.credit-sales.card');
        //

        //deposit
            Route::get('/{business}/withdrawal', [WithdrawalController::class, 'index'])->name('business.withdrawal.index');
            Route::get('/{business}/withdrawal/{id}/print-detail', [WithdrawalController::class, 'printDetail']);
        //

        //ledgers
            //Account Page
                Route::get('/{business}/account', [BusinessAccountController::class, 'index'])->name('business.account.index');
            //

            //Journal Page
                Route::get('/{business}/journal', [BusinessJournalController::class, 'index'])->name('business.journal.index');
                Route::get('/{business}/journal/create', [BusinessJournalController::class, 'create'])->name('business.journal.create');
                Route::get('/{business}/journal/{businessjournal}/edit', [BusinessJournalController::class, 'edit'])->name('business.journal.edit');
                Route::get('/{business}/journal/print-detail/{businessjournal}', [BusinessJournalController::class, 'printDetail']);
                Route::get('/{business}/journal/print', [BusinessJournalController::class, 'print']);
            //

            //Ledger Page
                Route::get('/{business}/ledger', [BusinessLedgerController::class, 'index'])->name('business.ledger.index');
                Route::get('/{business}/ledger/print', [BusinessLedgerController::class, 'print']);
            //
        //

        //inventory
            Route::get('/{business}/inventory-adjustment', [InventoryAdjustmentController::class, 'index'])->name('business.inventory-adjustment.index');
            Route::get('/{business}/inventory-adjustment/create', [InventoryAdjustmentController::class, 'create'])->name('business.inventory-adjustment.create');
            Route::get('/{business}/inventory-adjustment/{inventoryadjustment}/edit', [InventoryAdjustmentController::class, 'edit'])->name('business.inventory-adjustment.edit');
            Route::get('/{business}/inventory-adjustment/print-detail/{inventoryadjustment}', [InventoryAdjustmentController::class, 'printDetail']);
        //
        //stock opname
            Route::get('/{business}/stock-opname', [StockOpnameController::class, 'index'])->name('business.stock-opname.index');
            Route::get('/{business}/stock-opname/create', [StockOpnameController::class, 'create'])->name('business.stock-opname.create');
            Route::get('/{business}/stock-opname/{stockOpname}/edit', [StockOpnameController::class, 'edit'])->name('business.stock-opname.edit');
            Route::get('/{business}/stock-opname/print-detail/{stockOpname}', [StockOpnameController::class, 'printDetail']);
        //
        

        //Report Page
            //Cashflow Report Page
                Route::get('/{business}/report/cashflow', [BusinessCashflowReportController::class, 'index'])->name('report.business.cashflow.index');
                Route::get('/{business}/report/cashflow-year', [BusinessCashflowReportController::class, 'year'])->name('report.business.cashflow.year');
                Route::get('/{business}/report/print-cashflow', [BusinessCashflowReportController::class, 'print'])->name('report.business.cashflow.print');
                Route::get('/{business}/report/print-cashflow-year', [BusinessCashflowReportController::class, 'printYear'])->name('report.business.cashflow.print.year');
            //

            //Balance Report Page
                Route::get('/{business}/report/balance', [BusinessBalanceReportController::class, 'index'])->name('report.business.balance.index');
                Route::get('/{business}/report/balance-year', [BusinessBalanceReportController::class, 'year'])->name('report.business.balance.year');
                Route::get('/{business}/report/print-balance', [BusinessBalanceReportController::class, 'print'])->name('report.business.balance.print');
                Route::get('/{business}/report/print-balance-year', [BusinessBalanceReportController::class, 'printYear'])->name('report.business.balance.print.year');
            //

            //Lost Profit Report Page
                Route::get('/{business}/report/lost-profit', [BusinessLostProfitReportController::class, 'index'])->name('report.business.lost-profit.index');
                Route::get('/{business}/report/lost-profit-year', [BusinessLostProfitReportController::class, 'year'])->name('report.business.lost-profit.year');
                Route::get('/{business}/report/print-lost-profit', [BusinessLostProfitReportController::class, 'print'])->name('report.business.lost-profit.print');
                Route::get('/{business}/report/lost-profit-year-print', [BusinessLostProfitReportController::class, 'printYear'])->name('report.business.balance.print.year');
            //

            //Changes in equity
                Route::get('/{business}/report/changes-in-equity', [ChangesInEquityController::class, 'index'])->name('report.business.changes-in-equity.index');
                Route::get('/{business}/report/changes-in-equity-print', [ChangesInEquityController::class, 'print'])->name('report.business.balance.print.year');
            //

            //Trial Balance
                Route::get('/{business}/report/trial-balance', [BusinessTrialBalanceReportController::class, 'index'])->name('report.business.trial-balance.index');
                Route::get('/{business}/report/trial-balance-print', [BusinessTrialBalanceReportController::class, 'print'])->name('report.business.trial-balance.print.year');
            //
        //

        //Cash and Bank
            //revenue
                Route::get('/{business}/revenue', [BusinessRevenueController::class, 'index'])->name('business.revenue.index');
                Route::get('/{business}/revenue/create', [BusinessRevenueController::class, 'create'])->name('business.revenue.create');
                Route::get('/{business}/revenue/{businessrevenue}/edit', [BusinessRevenueController::class, 'edit'])->name('business.revenue.edit');
                Route::get('/{business}/revenue/print-detail/{businessrevenue}', [BusinessRevenueController::class, 'printDetail']);
                Route::get('/{business}/revenue/print', [BusinessRevenueController::class, 'print']);
            //
            //expense
                Route::get('/{business}/expense', [BusinessExpenseController::class, 'index'])->name('business.expense.index');
                Route::get('/{business}/expense/create', [BusinessExpenseController::class, 'create'])->name('business.expense.create');
                Route::get('/{business}/expense/{businessexpense}/edit', [BusinessExpenseController::class, 'edit'])->name('business.expense.edit');
                Route::get('/{business}/expense/print-detail/{businessexpense}', [BusinessExpenseController::class, 'printDetail']);
                Route::get('/{business}/expense/print', [BusinessExpenseController::class, 'print']);
            //
            //cash mutation
                Route::get('/{business}/cash-mutation', [BusinessCashMutationController::class, 'index'])->name('business.cash-mutation.index');
                Route::get('/{business}/cash-mutation/create', [BusinessCashMutationController::class, 'create'])->name('business.cash-mutation.create');
                Route::get('/{business}/cash-mutation/{businesscashmutation}/edit', [BusinessCashMutationController::class, 'edit'])->name('business.cash-mutation.edit');
                Route::get('/{business}/cash-mutation/print-detail/{businesscashmutation}', [BusinessCashMutationController::class, 'printDetail']);
                Route::get('/{business}/cash-mutation/print', [BusinessCashMutationController::class, 'print']);
            //
        //

        // Category Page 
            Route::get('/{business}/category', [CategoryController::class, 'index'])->name('business.category.index');
            Route::post('/{business}/category', [CategoryController::class, 'store'])->name('business.category.store');
            Route::delete('/{business}/category/{category}', [CategoryController::class, 'delete'])->name('business.category.delete');
        //

        // Brand Page
            Route::get('/{business}/brand', [BrandController::class, 'index'])->name('business.brand.index');
            Route::post('/{business}/brand', [BrandController::class, 'store'])->name('business.brand.store');
            Route::delete('/{business}/brand/{brand}', [BrandController::class, 'delete'])->name('business.brand.delete');
        // 

        // Supplier Page
            Route::get('/{business}/supplier', [SupplierController::class, 'index'])->name('business.supplier.index');
            Route::post('/{business}/supplier', [SupplierController::class, 'store'])->name('business.supplier.store');
            Route::patch('/{business}/supplier/{supplier}', [SupplierController::class, 'update'])->name('business.supplier.update');
            Route::delete('/{business}/supplier/{supplier}', [SupplierController::class, 'delete'])->name('business.supplier.delete');
        //

        // Customer Page
            Route::get('/{business}/customer', [CustomerController::class, 'index'])->name('business.customer.index');
            Route::post('/{business}/customer', [CustomerController::class, 'store'])->name('business.customer.store');
            Route::patch('/{business}/customer/{customer}', [CustomerController::class, 'update'])->name('business.customer.delete');
            Route::delete('/{business}/customer/{customer}', [CustomerController::class, 'delete'])->name('business.customer.delete');
        //

        // Product Page
            Route::get('/{business}/product', [ProductController::class, 'index'])->name('business.product.index');
            Route::get('/{business}/product/create', [ProductController::class, 'create'])->name('business.product.create');
            Route::get('/{business}/product/{product}/edit', [ProductController::class, 'edit'])->name('business.product.edit');
            Route::patch('/{business}/product/{product}', [ProductController::class, 'update'])->name('business.product.update');
            Route::delete('/{business}/product/{product}', [ProductController::class, 'delete'])->name('business.product.delete');
            Route::get('/{business}/product/print', [ProductController::class, 'print'])->name('business.product.print');
            Route::get('/{business}/product/print-stock', [ProductController::class, 'printStock'])->name('business.product.print-stock');
        // 

        // Incoming Item Page 
            Route::get('/{business}/incoming-item', [IncomingItemController::class, 'index'])->name('business.incoming-item.index');
            Route::get('/{business}/incoming-item/create', [IncomingItemController::class, 'create'])->name('business.incoming-item.create');
            Route::post('/{business}/incoming-item', [IncomingItemController::class, 'store'])->name('business.incoming-item.store');
            Route::patch('/{business}/incoming-item/stock/{stock}', [IncomingItemController::class, 'update'])->name('business.incoming-item.update');
            Route::delete('/{business}/incoming-item/stock/{stock}', [IncomingItemController::class, 'deleteStock'])->name('business.incoming-item.delete-stock');
        // 

        // Stock Page
            Route::get('/{business}/stock', [StockController::class, 'index'])->name('business.stock.index');
            Route::get('/{business}/stock/create', [StockController::class, 'create'])->name('business.stock.create');
            Route::post('/{business}/stock', [StockController::class, 'store'])->name('business.stock.store');
            Route::patch('/{business}/stock/{stock}', [StockController::class, 'update'])->name('business.stock.update');
            Route::delete('/{business}/stock/{stock}', [StockController::class, 'delete'])->name('business.stock.delete');

            // Excel
            Route::get('/{business}/stock/excel', function(Business $business){
                $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
                if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
                    return abort(403);
                } 
                try {                        
                    return Excel::download(new StockExport($business['id']), 'Laporan Stok Barang ' . $business['nama'] . '.xlsx');
                } catch (\Throwable $th) {
                    abort(503, 'Terjadi Kesalahan');
                } 
            })->name('business.stock.excel');

            // PDF
            Route::get('/{business}/stock/pdf', function(Business $business){        
                $identity = Identity::first();
        
                $products = Product::query()->whereHas('stock', function($query){
                                $query->where('jumlah', '>', 0);
                            })           
                            ->with('stock')             
                            ->where('business_id', $business['id'])
                            ->orderBy('created_at')
                            ->orderBy('kategori')
                            ->get();

                $total = 0;
                
                foreach ($products as $key => $product) {
                    $total += $product->modal * $product->stock->jumlah;
                }

                try {
                    $pdf = PDF::loadview('report.report-business-stock', [
                                                                    'products' => $products,
                                                                    'identity' => $identity,
                                                                    'business' => $business,
                                                                    'total' => $total
                                                                ]);
                    return $pdf->download('Laporan Stock ' . $business['nama'] . '.pdf');
                } catch (\Throwable $th) {
                    abort(503, 'Terjadi Kesalahan');
                }
                
            })->name('business.stock.pdf');
        // 

        // Asset Page
            Route::get('/{business}/asset', [AssetController::class, 'index'])->name('business.asset.index');
            Route::post('/{business}/asset', [AssetController::class, 'store'])->name('business.asset.store');
            Route::patch('/{business}/asset/{asset}', [AssetController::class, 'update'])->name('business.asset.update');
            Route::delete('/{business}/asset/{asset}', [AssetController::class, 'delete'])->name('business.asset.delete');

            // Excel
            Route::get('/{business}/asset/excel', function(Business $business){
                $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
                if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
                    return abort(403);
                } 

                try {                        
                    return (new AssetExport($business['id']))->download('Laporan Aset ' . $business['nama'] . '.xlsx');
                } catch (\Throwable $th) {
                    abort(503, 'Terjadi Kesalahan');
                } 
            })->name('business.asset.excel');

            // PDF
            Route::get('/{business}/asset/pdf', function(Business $business){        
                $identity = Identity::first();

                $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
                if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
                    return abort(403);
                } 
        
                $assets = Asset::where('business_id', $business['id'])->select('name_item','harga_satuan','jumlah_bagus', 'tanggal_masuk', 'kode', DB::raw('(jumlah_bagus * harga_satuan) as jumlah'))->get();
        
                try {
                    $pdf = PDF::loadview('report.report-business-asset', [
                                                                    'assets' => $assets,
                                                                    'identity' => $identity,
                                                                    'business' => $business,
                                                                    'total' => $assets->sum('jumlah')
                                                                ]);
                    return $pdf->download('Laporan Asset ' . $business['nama'] . '.pdf');
                } catch (\Throwable $th) {
                    abort(503, 'Terjadi Kesalahan');
                }
                
            })->name('business.asset.pdf');
        // 

        // Cashier Page
            Route::get('/{business}/cashier', [CashierController::class, 'index'])->name('business.cashier.index');
            Route::get('/{business}/cashier-restaurant', [CashierController::class, 'indexRestaurant'])->name('business.cashier.index-restaurant');
        // 

        //Invoices Page
            Route::get('/{business}/invoice', [InvoiceController::class, 'index'])->name('business.invoice.index');
            Route::get('/{business}/invoice/create', [InvoiceController::class, 'create'])->name('business.invoice.create');
            Route::get('/{business}/invoice/{id}/edit', [InvoiceController::class, 'edit']);
            Route::delete('/{business}/invoice/{id}/print-detail', [InvoiceController::class, 'destroy']);
            Route::get('/{business}/invoice/{id}/print-detail', [InvoiceController::class, 'printDetail']);
            Route::get('/{business}/invoice/print', [InvoiceController::class, 'print']);
        //

        //Purchase Goods Page
            Route::get('/{business}/purchase-goods', [PurchaseGoodsController::class, 'index'])->name('business.purchase-goods.index');
            Route::get('/{business}/purchase-goods/create', [PurchaseGoodsController::class, 'create'])->name('business.purchase-goods.create');
            Route::get('/{business}/purchase-goods/{id}/edit', [PurchaseGoodsController::class, 'edit']);
            Route::get('/{business}/purchase-goods/{id}/print-detail', [PurchaseGoodsController::class, 'printDetail']);
        // 

        // Daily Income Page
            // Retail
            Route::get('/{business}/daily-incomes', [DailyIncomeController::class, 'index'])->name('business.daily-income.index');
            Route::get('/{business}/daily-incomes/cashier', [DailyIncomeController::class, 'cashierDetail'])->name('business.daily-income.cashier-detail');
            Route::get('/{business}/daily-incomes/account-reserve-payment-detail', [DailyIncomeController::class, 'accountReservePaymentDetail'])->name('business.daily-income.account-reserve-payment-detail');
            Route::post('/{business}/daily-incomes/closing-income', [DailyIncomeController::class, 'closingIncome'])->name('business.daily-income.closing-income');

            //Restoran
            // Route::get('/{business}/daily-incomes/cashier-restaurant', [DailyIncomeController::class, 'cashierRestaurant'])->name('business.daily-income.cashier-restaurant');
        // 

        // Income Page
            Route::get('/{business}/business-income', [BusinessIncomeController::class, 'index'])->name('business.business-income.index');
            Route::patch('/{business}/business-income', [BusinessIncomeController::class, 'updateBusinessBalance'])->name('business.business-income.update-business-balance');

            // PDF
                Route::get('/{business}/business-income/pdf', function(Business $business, Request $request){
                    $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
                    if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
                        return abort(403);
                    } 

                    $identity = Identity::first();

                    if (($request->berdasarkan == 'month')) {
                        $invoices = Invoice::where('business_id', $business['id'])
                                        ->whereMonth('created_at', $request->bulan)
                                        ->whereYear('created_at', $request->tahun)
                                        ->with('products')
                                        ->get();
                    } else {
                        $invoices = Invoice::where('business_id', $business['id'])
                                        ->whereDate('created_at', '>=', $request->dari)
                                        ->whereDate('created_at', '<=', $request->ke)
                                        ->with('products')
                                        ->get();
                    }

                    $jabatanPembuat = $request->jabatan_pembuat;
                    $namaPembuat = $request->nama_pembuat;
                    $jabatanPenerima = $request->jabatan_penerima;
                    $namaPenerima = $request->nama_penerima;

                    $param = '';
                    if ($request->berdasarkan == 'month') {
                        $param = 'Per Bulan ' . MonthHelper::index($request->bulan) . ' ' . $request->tahun;
                    } else {
                        $param = 'Per Tanggal ' . $request->ke . ' s.d ' . $request->dari;
                    }

                    $businessUser = $business;
                    
                    try {
                        $pdf = PDF::loadview('report.report-business-income', [
                                                                        'invoices' => $invoices,
                                                                        'business' => $business,
                                                                        'identity' => $identity,
                                                                        'jabatanPembuat' => $jabatanPembuat,
                                                                        'namaPembuat' => $namaPembuat,
                                                                        'jabatanPenerima' => $jabatanPenerima,
                                                                        'namaPenerima' => $namaPenerima,
                                                                        'param' => $param,
                                                                    ]);
                        return $pdf->download('Laporan Penjualan ' . $business['nama'] . '.pdf');
                    } catch (\Throwable $th) {
                        abort(503, 'Terjadi Kesalahan');
                    }
                })->name('business.income.pdf');
            // 

            // Excel 
                Route::get('/{business}/business-income/excel', function(Business $business, Request $request){
                    $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
                    if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
                        return abort(403);
                    } 

                    $identity = Identity::first();

                    $invoices = Invoice::where('business_id', $business['id'])
                                        ->whereDate('created_at', '<=', $request->dari)
                                        ->whereDate('created_at', '>=', $request->ke)
                                        ->with('products')
                                        ->get();

                    $param = '';

                    if ($request->berdasarkan == 'date') {
                        $param = 'Per Tanggal ' . $request->ke . '-' . $request->dari;
                    } else {
                        $param = 'Per Bulan ' . MonthHelper::index($request->bulan) . ' ' . $request->tahun;
                    }

                    try {                        
                        return Excel::download(new BusinessIncomeExport($business['id'], $request->dari, $request->ke), 'Laporan Penjualan '. $param . ' ' . $business['nama'] . '.xlsx');
                    } catch (\Throwable $th) {
                        abort(503, 'Terjadi Kesalahan');
                    } 
                })->name('business.income.excel');
            // 

        //

        //Outcome Page
            // Route::get('/{business}/expense', [DailyOutcomeController::class, 'index'])->name('business.expense.index');
            // Route::post('/{business}/expense', [DailyOutcomeController::class, 'store'])->name('business.expense.store');
            // Route::patch('/{business}/expense/{expense}', [DailyOutcomeController::class, 'update'])->name('business.expense.update');
            // Route::delete('/{business}/expense/{expense}', [DailyOutcomeController::class, 'delete'])->name('business.expense.delete');

            // Excel
            Route::get('/{business}/business-expense/excel', function(Business $business, Request $request){
                $tanggal_awal = $request['tanggal_awal'];
                $tanggal_akhir = $request['tanggal_akhir'];
                
                
                try {                        
                    return (new BusinessExpenseExport($business['id'], $tanggal_awal, $tanggal_akhir))->download('Laporan Pengeluaran ' . $business['nama'] . '.xlsx');
                } catch (\Throwable $th) {
                    abort(503, 'Terjadi Kesalahan');
                } 
            })->name('business.expense.excel');

            // PDF
            Route::get('/{business}/business-expense/pdf', function(Business $business, Request $request){
                $tanggal_awal = $request['tanggal_awal'];
                $tanggal_akhir = $request['tanggal_akhir'];
        
                $identity = Identity::first();
        
                $expenses = ($tanggal_awal && $tanggal_akhir) ? 
                            BusinessExpense::where('business_id', $business['id'])->whereBetween('tanggal_keluar', [$tanggal_awal, $tanggal_akhir])->orderBy('tanggal_keluar', 'asc')->get()
                            : BusinessExpense::where('business_id', $business['id'])->orderBy('tanggal_keluar', 'asc')->get();
                            
                try {
                    $pdf = PDF::loadview('report.report-business-expense', [
                                                                    'expenses' => $expenses,
                                                                    'identity' => $identity,
                                                                    'business' => $business,
                                                                    'total' => $expenses->sum('jumlah')
                                                                ]);
                    return $pdf->download('Laporan Pengeluaran ' . $business['nama'] . '.pdf');
                } catch (\Throwable $th) {
                    abort(503, 'Terjadi Kesalahan');
                }
                
            })->name('business.expense.pdf');

        // 

        // Finance Page 
            //account receivable
                Route::get('/{business}/account-receivable', [AccountReceivableController::class, 'index'])->name('business.account-receivable.index');
                Route::post('/{business}/account-receivable', [AccountReceivableController::class, 'store'])->name('business.account-receivable.store');
                Route::get('/{business}/pay-later', [AccountReceivableController::class, 'payLater'])->name('business.account-receivable.pay-later');
            //
            //account receivable payment
                Route::get('/{business}/account-receivable-payment', [AccountReceivablePaymentController::class, 'index'])->name('business.account-receivable-payment.index');
                Route::get('/{business}/account-receivable-payment/create', [AccountReceivablePaymentController::class, 'store'])->name('business.account-receivable-payment.create');
                Route::get('/{business}/account-receivable-payment/{id}/print-detail', [AccountReceivablePaymentController::class, 'printDetail']);
                Route::get('/{business}/pay-later', [AccountReceivableController::class, 'payLater'])->name('business.account-receivable.pay-later');
            //

            //over due
                Route::get('/{business}/over-due', [OverDueController::class, 'index'])->name('business.over-due.index');
            //

            //account payable
                Route::get('/{business}/account-payable', [AccountPayableController::class, 'index'])->name('business.account-payable.index');
                Route::post('/{business}/account-payable', [AccountPayableController::class, 'store'])->name('business.account-payable.store');
                Route::get('/{business}/pay-later', [AccountPayableController::class, 'payLater'])->name('business.account-payable.pay-later');
            //
            //account payable payment
                Route::get('/{business}/account-payable-payment', [AccountPayablePaymentController::class, 'index'])->name('business.account-payable-payment.index');
                Route::get('/{business}/account-payable-payment/create', [AccountPayablePaymentController::class, 'store'])->name('business.account-payable-payment.create');
                Route::get('/{business}/account-payable-payment/{id}/print-detail', [AccountPayablePaymentController::class, 'printDetail']);
                Route::get('/{business}/pay-later', [AccountPayableController::class, 'payLater'])->name('business.account-payable.pay-later');
            //
        // 
    //
    
});