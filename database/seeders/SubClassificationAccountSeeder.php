<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubClassificationAccount;
use App\Models\SubClassifitacionAccount;

class SubClassificationAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => 'Kas dan Setara Kas', 'code' => '1100000'],
            ['name' => 'Bank', 'code' => '1200000'],
            ['name' => 'Piutang Usaha', 'code' => '1300000'],
            ['name' => 'Piutang Lain', 'code' => '1399000'],
            ['name' => 'Persediaan Barang Dagang', 'code' => '1400000'],
            ['name' => 'Persediaan Lain', 'code' => '1499000'],
            ['name' => 'Uang Dibayar Dimuka', 'code' => '1510000'],
            ['name' => 'Pajak Dibayar Dimuka', 'code' => '1520000'],
            ['name' => 'Biaya Dibayar Dimuka', 'code' => '1530000'],
            ['name' => 'Biaya Belum Ditagih', 'code' => '1590000'],
            ['name' => 'Investasi', 'code' => '1600000'],
            ['name' => 'Harta Unit Usaha', 'code' => '1900000'],
            ['name' => 'Harta Tetap Berwujud', 'code' => '1700000'],
            ['name' => 'Akumulasi Penyusutan Harta Tetap', 'code' => '1710000'],
            ['name' => 'Harta Tetap Tidak Berwujud', 'code' => '1800000'],
            ['name' => 'Utang Usaha', 'code' => '2100000'],
            ['name' => 'Utang Lain', 'code' => '2190000'],
            ['name' => 'Uang Muka Diterima', 'code' => '2210000'],
            ['name' => 'Pendapatan Belum Ditagihkan', 'code' => '2290000'],
            ['name' => 'Utang Pajak', 'code' => '2300000'],
            ['name' => 'Utang Jangka Panjang', 'code' => '2600000'],
            ['name' => 'Modal', 'code' => '3100000'],
            ['name' => 'Laba', 'code' => '3200000'],
            ['name' => 'Modal Unit Usaha', 'code' => '3900000'],
            ['name' => 'Penjualan Produk', 'code' => '4100000'],
            ['name' => 'Potongan Penjualan', 'code' => '4200000'],
            ['name' => 'Retur Penjualan', 'code' => '4300000'],
            ['name' => 'Pendapatan Lain', 'code' => '4900000'],
            ['name' => 'Harga Pokok Penjualan', 'code' => '5100000'],
            ['name' => 'Potongan Pembelian', 'code' => '5200000'],
            ['name' => 'Retur Pembelian', 'code' => '5300000'],
            ['name' => 'Beban Pajak', 'code' => '5600000'],
            ['name' => 'Beban Penyusutan', 'code' => '5700000'],
            ['name' => 'Beban Operasional', 'code' => '5800000'],
            ['name' => 'Beban Atas Pendapatan', 'code' => '5900000'],
        ];

        foreach ($data as $d) {
            SubClassificationAccount::create($d);
        }
    }
}
