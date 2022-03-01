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
use Illuminate\Http\Request;
use App\Exports\IncomeExport;

use App\Exports\OutcomeExport;
use App\Imports\RegencyImport;
use App\Imports\VillageImport;
use App\Imports\DistrictImport;
use App\Imports\ProvinceImport;
use App\Models\BusinessExpense;
use App\Helpers\BusinessUserHelper;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BusinessIncomeExport;
use Illuminate\Support\Facades\Route;
use App\Exports\BusinessExpenseExport;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\OutcomeController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\IdentityController;
use App\Http\Controllers\Business\AssetController;
use App\Http\Controllers\Business\BrandController;
use App\Http\Controllers\Business\StockController;
use App\Http\Controllers\Business\CashierController;
use App\Http\Controllers\Business\ProductController;
use App\Http\Controllers\Business\CategoryController;
use App\Http\Controllers\Business\CustomerController;
use App\Http\Controllers\Business\SupplierController;
use App\Http\Controllers\Business\DashboardController;
use App\Http\Controllers\Business\DailyIncomeController;
use App\Http\Controllers\Business\DailyOutcomeController;
use App\Http\Controllers\Business\IncomingItemController;
use App\Http\Controllers\Business\BusinessIncomeController;
use App\Http\Controllers\Business\AccountReceivableController;
use App\Http\Controllers\Business\BusinessBalanceActivityController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::group(['middleware' => ['auth']], function(){

    Route::group(['middleware' => ['admin']], function(){
        Route::get('/admin', function(){
            $outcome = Outcome::all()->sum('jumlah');
            $income = Income::all()->sum('jumlah');
    
            return view('admin.dashboard', [
                'saldo' => $income - $outcome
            ]);
        })->name('admin.dashboard');
    
    
        Route::resource('/identity', IdentityController::class)->middleware('admin');
    
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
            Route::post('/{business}/product', [ProductController::class, 'store'])->name('business.product.store');
            Route::patch('/{business}/product/{product}', [ProductController::class, 'update'])->name('business.product.update');
            Route::delete('/{business}/product/{product}', [ProductController::class, 'delete'])->name('business.product.delete');
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

                    $invoices = Invoice::where('business_id', $business['id'])
                                        ->whereDate('created_at', '<=', $request->dari)
                                        ->whereDate('created_at', '>=', $request->ke)
                                        ->with('products')
                                        ->get();

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

                    return Excel::download(new BusinessIncomeExport($business['id'], $request->dari, $request->ke), 'Laporan Penjualan '. $param . ' ' . $business['nama'] . '.xlsx');
                    
                    try {                        
                        return Excel::download(new BusinessIncomeExport($business['id']), 'Laporan Penjualan '. $param . ' ' . $business['nama'] . '.xlsx');
                    } catch (\Throwable $th) {
                        abort(503, 'Terjadi Kesalahan');
                    } 
                })->name('business.income.excel');
            // 

        //

        //Outcome Page
            Route::get('/{business}/expense', [DailyOutcomeController::class, 'index'])->name('business.expense.index');
            Route::post('/{business}/expense', [DailyOutcomeController::class, 'store'])->name('business.expense.store');
            Route::patch('/{business}/expense/{expense}', [DailyOutcomeController::class, 'update'])->name('business.expense.update');
            Route::delete('/{business}/expense/{expense}', [DailyOutcomeController::class, 'delete'])->name('business.expense.delete');

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
            Route::get('/{business}/account-receivable', [AccountReceivableController::class, 'index'])->name('business.account-receivable.index');
            Route::post('/{business}/account-receivable', [AccountReceivableController::class, 'store'])->name('business.account-receivable.store');
            Route::get('/{business}/pay-later', [AccountReceivableController::class, 'payLater'])->name('business.account-receivable.pay-later');

            
        // 
    //
    
});