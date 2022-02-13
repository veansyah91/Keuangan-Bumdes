<?php

namespace App\Exports;

use App\Models\Income;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IncomeExport implements FromQuery, WithHeadings
{
    use Exportable;
    
    public function __construct($tanggal_awal, $tanggal_akhir)
    {
        $this->tanggal_awal = $tanggal_awal;
        $this->tanggal_akhir = $tanggal_akhir;
    }

    public function headings(): array
    {
        return [
            'Tanggal Masuk',
            'Nama Item',
            'Harga',
            'Jumlah',
        ];
    }

    public function query()
    {
        
        $incomes = ($this->tanggal_awal && $this->tanggal_akhir ) ? 
                      Income::query()->whereBetween('tanggal_masuk', [$this->tanggal_awal, $this->tanggal_akhir ])->orderBy('tanggal_masuk', 'asc')->select('jumlah','keterangan','tanggal_masuk')
                    : Income::query()->orderBy('tanggal_masuk', 'asc')->select('tanggal_masuk','jumlah','keterangan');
                    
                    
        return $incomes;
    }
}
