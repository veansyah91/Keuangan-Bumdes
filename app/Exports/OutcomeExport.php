<?php

namespace App\Exports;

use App\Models\Outcome;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OutcomeExport implements FromQuery, WithHeadings
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($tanggal_awal, $tanggal_akhir)
    {
        $this->tanggal_awal = $tanggal_awal;
        $this->tanggal_akhir = $tanggal_akhir;
        
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
        $outcomes = ($this->tanggal_awal && $this->tanggal_akhir ) ? 
                      Outcome::query()->whereBetween('tanggal_keluar', [$this->tanggal_awal, $this->tanggal_akhir ])->orderBy('tanggal_keluar', 'asc')->select('jumlah','keterangan','tanggal_keluar')
                    : Outcome::query()->orderBy('tanggal_keluar', 'asc')->select('tanggal_keluar','jumlah','keterangan');
                    
                    
        return $outcomes;
    }
}
