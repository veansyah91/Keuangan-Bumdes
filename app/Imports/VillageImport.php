<?php

namespace App\Imports;

use App\Models\Village;
use Maatwebsite\Excel\Concerns\ToModel;

class VillageImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    
    public function model(array $row)
    {
        return new Village([
            'kode' => $row[0],
            'kode_kecamatan' => $row[1],
            'nama' => $row[2],
        ]);
    }
}
