<?php

namespace App\Exports;

use App\Models\BusinessExpense;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BusinessExpenseExport implements FromQuery, WithHeadings
{
    use Exportable;
    
    public function __construct($businessId, $tanggal_awal, $tanggal_akhir)
    {
        $this->tanggal_awal = $tanggal_awal;
        $this->tanggal_akhir = $tanggal_akhir;
        $this->businessId = $businessId;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jumlah',
            'Keterangan',
        ];
    }

    public function query()
    {
        
        $expenses = ($this->tanggal_awal && $this->tanggal_akhir ) ? 
                      BusinessExpense::query()->where('business_id', $this->businessId)->whereBetween('tanggal_keluar', [$this->tanggal_awal, $this->tanggal_akhir ])->orderBy('tanggal_keluar', 'asc')->select('jumlah','keterangan','tanggal_keluar')
                    : BusinessExpense::query()->where('business_id', $this->businessId)->orderBy('tanggal_keluar', 'asc')->select('tanggal_keluar','jumlah','keterangan');
                    
                    
        return $expenses;
    }
}
