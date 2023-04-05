<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Contact;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\Businessaccount;
use App\Models\SubClassificationAccount;

class BusinessController extends Controller
{
    public function newNoRefContact($no_ref_request, $no_ref_contact){
        $split_contact_ref_no = explode("-", $no_ref_contact);
        $old_ref_no = (int)$split_contact_ref_no[1];
        $new_ref_no = 1000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }

    public function index()
    {
        $businesses = Business::orderBy('created_at', 'desc')->get();

        return view('admin.business.index', [
            'businesses' => $businesses 
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required'
        ]);

        $business = Business::create([
            'nama' => $validatedData['nama'],
            'kategori' => $request->kategori,
            'status' => 'active',
        ]);

        $ref = 'BUSS';
        
        $fix_ref_no = '';

        $contact = Contact::where('no_ref', 'like', $ref . '%')->orderBy('id', 'desc')->first();

        if($contact){
            $fix_ref_no = $this->newNoRefContact($ref, $contact->no_ref);
        }else{
            $fix_ref_no = $ref . '-001';
        }

        // buat akun 
        //kategori asset
        $account = Account::where('name', 'like', 'Harta Unit Usaha%')->orderBy('code', 'desc')->first();

        if ($account) {
            //create account based on business name
            Account::create([
                'name' => 'Harta Unit Usaha ' . $validatedData['nama'],
                'code' => (string)((int)$account['code'] + 1),
                'is_cash' => false,
                'is_active' => true,
                'sub_classification_account_id' => $account['sub_classification_account_id '],
                'sub_category' => $account['sub_category'],
            ]);
        }


        // buat data contact
        Contact::create([
            'no_ref' => $fix_ref_no,
            'name' => $validatedData['nama'],
            'type' => 'Business'
        ]);

        //kategori modal
        $account = Account::where('name', 'like', 'Modal Unit Usaha%')->first();
        if ($account) {
            //create account based on business name
            Account::create([
                'name' => 'Modal Unit Usaha ' . $validatedData['nama'],
                'code' => (string)((int)$account['code'] + 1),
                'is_cash' => false,
                'is_active' => true,
                'sub_classification_account_id' => $account['sub_classification_account_id '],
                'sub_category' => $account['sub_category'],
            ]);
        }

        //buat akun
        $accounts = [
            [
                'code' => '1100001',
                'name' => 'Kas',
                'is_cash' => true,
                'is_active' => true,
                'sub_category' => 'Kas dan Setara Kas'
            ],
            [
                'code' => '1100002',
                'name' => 'Kas Kecil',
                'is_cash' => true,
                'is_active' => true,
                'sub_category' => 'Kas dan Setara Kas'
            ],
            [
                'code' => '1200001',
                'name' => 'Bank',
                'is_cash' => true,
                'is_active' => true,
                'sub_category' => 'Bank'
            ],
            [
                'code' => '1300001',
                'name' => 'Piutang Dagang',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Piutang Usaha'
            ],
            [
                'code' => '1310001',
                'name' => 'Piutang Nasabah Simpan Pinjam',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Piutang Nasabah'
            ],
            [
                'code' => '1399001',
                'name' => 'Piutang Lain',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Piutang Lain'
            ],
            [
                'code' => '1400001',
                'name' => 'Persediaan Barang Dagang',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Persediaan Barang Dagang'
            ],
            [
                'code' => '1499001',
                'name' => 'Persediaan Lain',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Persediaan Lain'
            ],
            [
                'code' => '1510001',
                'name' => 'Uang Dibayar Dimuka',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Uang Dibayar Dimuka'
            ],
            [
                'code' => '1520001',
                'name' => 'Pajak Dibayar Dimuka',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Pajak Dibayar Dimuka'
            ],
            [
                'code' => '1530001',
                'name' => 'Biaya Dibayar Dimuka',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Biaya Dibayar Dimuka'
            ],
            [
                'code' => '1530002',
                'name' => 'Sewa Dibayar Dimuka',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Biaya Dibayar Dimuka'
            ],
            [
                'code' => '1539002',
                'name' => 'Biaya Dibayar Dimuka Lainnya',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Biaya Dibayar Dimuka'
            ],
            [
                'code' => '1600000',
                'name' => 'Investasi Saham',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Investasi'
            ],
            [
                'code' => '1700001',
                'name' => 'Tanah',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Harta Tetap Berwujud'
            ],[
                'code' => '1700002',
                'name' => 'Bangunan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Harta Tetap Berwujud'
            ],
            [
                'code' => '1700002',
                'name' => 'Mesin dan Peralatan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Harta Tetap Berwujud'
            ],
            [
                'code' => '1700003',
                'name' => 'Kendaraan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Harta Tetap Berwujud'
            ],
            [
                'code' => '1710002',
                'name' => 'Akumulasi Penyusutan Bangunan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Akumulasi Penyusutan Harta Tetap'
            ],
            [
                'code' => '1710002',
                'name' => 'Akumulasi Penyusutan Mesin dan Peralatan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Akumulasi Penyusutan Harta Tetap'
            ],
            [
                'code' => '1710003',
                'name' => 'Akumulasi Penyusutan Kendaraan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Akumulasi Penyusutan Harta Tetap'
            ],
            [
                'code' => '1709003',
                'name' => 'Harta Lain',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Harta Tetap Berwujud'
            ],
            [
                'code' => '1719003',
                'name' => 'Akumulasi Penyusutan Harta Lain',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Akumulasi Penyusutan Harta Tetap'
            ],
            [
                'code' => '1900000',
                'name' => 'Harta Unit Usaha',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Harta Unit Usaha'
            ],
            [
                'code' => '2100001',
                'name' => 'Utang Usaha',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Utang Usaha'
            ],
            [
                'code' => '2180001',
                'name' => 'Tabungan Nasabah',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Tabungan Nasabah'
            ],
            [
                'code' => '2190001',
                'name' => 'Utang Belum Ditagih',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Utang Usaha'
            ],
            [
                'code' => '2190002',
                'name' => 'Utang Giro',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Utang Usaha'
            ],
            [
                'code' => '2210000',
                'name' => 'Uang Muka Penjualan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Uang Muka Diterima'
            ],
            [
                'code' => '2290000',
                'name' => 'Pendapatan Belum Ditagihkan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Pendapatan Belum Ditagihkan'
            ],
            [
                'code' => '2300000',
                'name' => 'Utang Pajak',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Utang Pajak'
            ],
            [
                'code' => '2600000',
                'name' => 'Utang Pembiayaan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Utang Jangka Panjang'
            ],
            [
                'code' => '3100001',
                'name' => 'Penyertaan Modal Desa',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Penyertaan Modal Desa'
            ],
            [
                'code' => '3110001',
                'name' => 'Penyertaan Modal Masyarakat',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Penyertaan Modal Masyarakat'
            ],
            [
                'code' => '3400001',
                'name' => 'Bagi Hasil Penyertaan Modal Desa',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Bagi Hasil Penyertaan Modal Desa'
            ],
            [
                'code' => '3410001',
                'name' => 'Bagi Hasil Penyertaan Modal Masyarakat',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Bagi Hasil Penyertaan Modal Masyarakat'
            ],
            [
                'code' => '3600001',
                'name' => 'Laba Ditahan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Laba Ditahan'
            ],
            [
                'code' => '3700001',
                'name' => 'Laba Tahun Berjalan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Laba Tahun Berjalan'
            ],   
            [
                'code' => '3200001',
                'name' => 'Modal Unit Usaha',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Modal Unit Usaha'
            ],            
            [
                'code' => '4100001',
                'name' => 'Penjualan Produk',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Penjualan Produk'
            ],
            [
                'code' => '4200001',
                'name' => 'Potongan Penjualan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Potongan Penjualan'
            ],
            [
                'code' => '4300001',
                'name' => 'Retur Penjualan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Retur Penjualan'
            ],
            [
                'code' => '4900001',
                'name' => 'Penjualan Jasa',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Pendapatan Lain'
            ],
            [
                'code' => '5100001',
                'name' => 'Harga Pokok Penjualan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Harga Pokok Penjualan'
            ],
            [
                'code' => '5900001',
                'name' => 'Potongan Pembelian',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Beban Atas Pendapatan'
            ],
            [
                'code' => '5900002',
                'name' => 'Beban Pembelian',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Beban Atas Pendapatan'
            ],
            [
                'code' => '5900003',
                'name' => 'Beban Pengiriman',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Beban Atas Pendapatan'
            ],
            [
                'code' => '5900004',
                'name' => 'Penyesuaian Persediaan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Beban Atas Pendapatan'
            ],
            [
                'code' => '5300000',
                'name' => 'Retur Pembelian',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Retur Pembelian'
            ],
            [
                'code' => '5200001',
                'name' => 'Potongan Pembelian',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Potongan Pembelian'
            ],
            [
                'code' => '5400001',
                'name' => 'Beban Piutang',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Beban Piutang'
            ],
            [
                'code' => '5800001',
                'name' => 'Beban Listrik',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Beban Operasional'
            ],
            [
                'code' => '5800002',
                'name' => 'Beban Gaji',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Beban Operasional'
            ],
            [
                'code' => '5800003',
                'name' => 'Beban Air',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Beban Operasional'
            ],
            [
                'code' => '5899999',
                'name' => 'Beban Lain',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Beban Operasional'
            ],
            [
                'code' => '5400001',
                'name' => 'Beban Piutang',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Beban Piutang'
            ],
            [
                'code' => '5600001',
                'name' => 'Beban Pajak',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Beban Pajak'
            ],
            [
                'code' => '5700001',
                'name' => 'Beban Penyusutan',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Beban Penyusutan'
            ],
            [
                'code' => '5999000',
                'name' => 'Ikhtisar Laba Rugi',
                'is_cash' => false,
                'is_active' => true,
                'sub_category' => 'Ikhtisar Laba Rugi'
            ],
        ];

        foreach ($accounts as $account) {
            $account['business_id'] = $business['id'];

            //find single data sub classification account by finding the sub_category
            $subClassification = SubClassificationAccount::where('name', $account['sub_category'])->first();

            if ($subClassification) {
                $account['sub_classification_account_id'] = $subClassification->id;
            
                if ($subClassification) {
                    Businessaccount::create($account);
                }
            }

            
        }

        return redirect('/business');
    }

    public function show(Business $business)
    {
        return response()->json([
            'status' => 'success',
            'data' => $business,
        ]); 
    }

    public function update(Request $request, Business $business)
    {
        $validatedData = $request->validate([
            'nama' => 'required'
        ]);

        $contact = Contact::where('name', $business['nama'])->first();
        // update data contact
        if ($contact) {
            $contact->update([
                'name' => $validatedData['nama'],
            ]);
        }

        // buat akun 
        //kategori asset
        $name_account = 'Harta Unit Usaha ' . $business['nama'];
        $account = Account::where('name', $name_account)->first();
        $account->update([
            'name' => 'Harta Unit Usaha ' . $validatedData['nama'],
        ]);

        //kategori modal
        $name_account = 'Modal Unit Usaha ' . $business['nama'];
        $account = Account::where('name', $name_account)->first();
        $account->update([
            'name' => 'Modal Unit Usaha ' . $validatedData['nama'],
        ]);

        $business->update([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'status' => $request->status,
        ]);

        return redirect('/business');
    }
}
