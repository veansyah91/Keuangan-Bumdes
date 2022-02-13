<?php

namespace App\Exports;

use App\Models\Asset;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class AssetExport implements FromQuery, WithHeadings
{
    use Exportable;
    
    public function __construct($businessId)
    {
        $this->businessId = $businessId;
    }

    public function headings(): array
    {
        return [
            'Tanggal Masuk',
            'Kode',
            'Nama Item',
            'Harga',
            'Qty',            
            'Jumlah',
        ];
    }

    public function query()
    {
        return Asset::query()->where('business_id', $this->businessId)->where('jumlah_bagus', '>', 0)->select('tanggal_masuk','kode', 'name_item', 'harga_satuan', 'jumlah_bagus', DB::raw('(jumlah_bagus * harga_satuan) as jumlah'));
    }
}
