<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StockExport implements FromView
{    
    public function __construct($businessId)
    {
        $this->businessId = $businessId;
    }

    public function view(): View
    {
        return view('export-excel.stock', [
            'products' => Product::query()->whereHas('stock', function($query){
                                $query->where('jumlah', '>', 0);
                            })           
                            ->with('stock')             
                            ->where('business_id', $this->businessId)
                            ->get()
        ]);
    }
}
