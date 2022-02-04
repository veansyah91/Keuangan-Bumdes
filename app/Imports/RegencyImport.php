<?php

namespace App\Imports;

use App\Models\Regency;
use Maatwebsite\Excel\Concerns\ToModel;

class RegencyImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Regency([
            'kode' => $row[0],
            'kode_provinsi' => $row[1],
            'nama' => $row[2],
        ]);
    }
}
