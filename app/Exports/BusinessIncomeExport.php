<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\Business;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class BusinessIncomeExport implements FromView
{
    public function __construct($businessId, $dari, $ke)
    {
        $this->businessId = $businessId;
        $this->dari = $dari;
        $this->ke = $ke;
    }

    public function view(): View
    {
        $invoices = Invoice::where('business_id', $this->businessId)
                                        ->whereDate('created_at', '<=', $this->dari)
                                        ->whereDate('created_at', '>=', $this->ke)
                                        ->with('products')
                                        ->get();
        $business = Business::find($this->businessId);
        return view('export-excel.business-income', [
            'invoices' => $invoices,
            'business' => $business,
        ]);
    }
}
