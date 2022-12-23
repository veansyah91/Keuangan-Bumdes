<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Account;
use App\Models\Journal;
use App\Models\Cashflow;
use App\Models\FixedAsset;
use Illuminate\Http\Request;
use App\Http\Resources\FixedAssetResource;
use Illuminate\Validation\ValidationException;

class FixedAssetController extends Controller
{
    public function newNoRefFixedAsset($no_ref_request, $no_ref_contact){
        $split_contact_ref_no = explode("-", $no_ref_contact);
        $old_ref_no = (int)$split_contact_ref_no[1];
        $new_ref_no = 1000000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }
    
    public function noRefFixedAssetRecomendation(){
        $fix_ref_no = '';

        $fixedAsset = FixedAsset::orderBy('id', 'desc')->first();

        if($fixedAsset){
            $fix_ref_no = $this->newNoRefFixedAsset('FA', $fixedAsset->no_ref);
        }else{
            $fix_ref_no = 'FA-000001';
        }

        return response()->json([
            'status' => 'success',
            'data' => $fix_ref_no,
        ]);
    }

    public function getData(){
        $fixedAsset = FixedAsset::filter(request(['search']))
                                ->orderBy('is_active', 'desc')
                                ->orderBy('date', 'desc')
                                ->orderBy('id', 'desc')
                                ->paginate(50);

        return response()->json([
            'status' => 'success',
            'data' => $fixedAsset,
        ]); 
    }

    public function index()
    {
        return view('admin.fixed-asset.index');
    }

    public function store(Request $request)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',//
            'name' => 'required',
            'date' => 'required|date',//
            'value' => 'required|numeric',//
            'salvage' => 'required|numeric',
            'useful_life' => 'required|numeric',
        ]);

        $attributes['is_active'] = true;
        $attributes['desc'] = "Pengadaan Harta Tetap";
        $attributes['detail'] = "Pengadaan Harta Tetap - " . $attributes['name'];
        $attributes['author'] = $request->user()->name;
        $attributes['source'] = 'Dari Pengadaan Harta Tetap';

        $fixedAsset = FixedAsset::create($attributes);

        //buat akun Harta Tetap, Penyusutan Harta Tetap, dan Beban Penyusutan Harta Tetap
        //asset
        $account = Account::where('sub_category', 'Harta Tetap Berwujud')->orderBy('id', 'desc')->first();
        
        $account_to_ledger = Account::create([
            'name' => $attributes['name'],
            'code' => (int)$account['code'] + 1,
            'is_active' => true,
            'is_cash' => false,
            'sub_category' => 'Harta Tetap Berwujud',
            'sub_classification_account_id' => $account['sub_classification_account_id']
        ]);

        //Penyusutan Harta Tetap
        $account = Account::where('sub_category', 'Akumulasi Penyusutan Harta Tetap')->orderBy('id', 'desc')->first();

        Account::create([
            'name' => 'Akumulasi Penyusutan ' . $attributes['name'],
            'code' => (int)$account['code'] + 1,
            'is_active' => true,
            'is_cash' => false,
            'sub_category' => 'Akumulasi Penyusutan Harta Tetap',
            'sub_classification_account_id' => $account['sub_classification_account_id']
        ]);

         //Beban Penyusutan Harta Tetap
         $account = Account::where('sub_category', 'Beban Penyusutan')->orderBy('id', 'desc')->first();

         Account::create([
             'name' => 'Beban Penyusutan ' . $attributes['name'],
             'code' => (int)$account['code'] + 1,
             'is_active' => true,
             'is_cash' => false,
             'sub_category' => 'Beban Penyusutan',
             'sub_classification_account_id' => $account['sub_classification_account_id']
         ]);

        //masukkan data ke jurnal 
        $journal = Journal::create($attributes);

        //masukkan data ke buku besar
        //debit
        Ledger::create([
            'account_name' => $account_to_ledger['name'],
            'account_id' => $account_to_ledger['id'],
            'account_code' => $account_to_ledger['code'],
            'no_ref' => $attributes['no_ref'],
            'debit' => $attributes['value'],
            'credit' => 0,
            'date' => $attributes['date'],
            'description' => $attributes['desc'],
            'author' => $attributes['author'],
            'note' => $attributes['detail'],
        ]);

        //credit
        $account = Account::where('id', $request->credit_account['id'])->first();

        //debit
        Ledger::create([
            'account_name' => $account['name'],
            'account_id' => $account['id'],
            'account_code' => $account['code'],
            'no_ref' => $attributes['no_ref'],
            'debit' => 0,
            'credit' => $attributes['value'],
            'date' => $attributes['date'],
            'description' => $attributes['desc'],
            'author' => $attributes['author'],
            'note' => $attributes['detail'],
        ]);

        //jika debit akun adalah kas maka masukkan ke tabel arus kas dengan 
        if ((int)$account['code'] < 1300001) {
            Cashflow::create([
                'account_id' => $account['id'],
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'account_code' => $account['code'],
                'account_name' => $account['name'],
                'type' => 'investment',
                'debit' => 0,
                'credit' => $attributes['value'],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]); 
    }

    public function show(FixedAsset $fixedAsset)
    {
        $ledger = Ledger::where('no_ref', $fixedAsset['no_ref'])->where('credit', '>', 0)->first();

        $account = Account::where('name', $fixedAsset['name'])->first();

        $fixedAsset['debit_account'] =[
            "id" => $account['id'],
            "code" => $account['code'],
            "name" => $account['name']
        ];

        $fixedAsset['credit_account'] =[
            "id" => $ledger['account_id'],
            "code" => $ledger['account_code'],
            "name" => $ledger['account_name']
        ];

        $name_account = 'Akumulasi Penyusutan ' . $fixedAsset['name'];
        $account = Account::where('name', $name_account)->first();
        $ledgers = Ledger::where('account_id', $account['id'])->get();

        $fixedAsset['depreciates'] = $ledgers;

        return response()->json([
            'status' => 'success',
            'data' => $fixedAsset,
        ]); 
    }

    public function edit(FixedAsset $fixedAsset)
    {
        $ledger = Ledger::where('no_ref', $fixedAsset['no_ref'])->where('credit', '>', 0)->first();

        $fixedAsset['credit_account'] =[
            "id" => $ledger['account_id'],
            "name" => $ledger['account_name']
        ];
        return response()->json([
            'status' => 'success',
            'data' => $fixedAsset,
        ]); 
    }


    public function update(Request $request, FixedAsset $fixedAsset)
    {
         // validasi input revenue 
         $attributes = $request->validate([
            'no_ref' => 'required',//
            'name' => 'required',
            'date' => 'required|date',//
            'value' => 'required|numeric',//
            'salvage' => 'required|numeric',
            'useful_life' => 'required|numeric',
        ]);

        $attributes['is_active'] = $request->is_active;

        //asset 
        $account_to_ledger = Account::where('sub_category', 'Harta Tetap Berwujud')->where('name', $fixedAsset['name'])->first();
        
        $account_to_ledger->update([
            'name' => $attributes['name'],
        ]);

        //Penyusutan Harta Tetap
        $account = Account::where('sub_category', 'Akumulasi Penyusutan Harta Tetap')->where('name', 'Akumulasi Penyusutan ' . $fixedAsset['name'])->first();
        
        $account->update([
            'name' => 'Akumulasi Penyusutan ' . $attributes['name'],
        ]);

        //Beban Penyusutan Harta Tetap
        $account = Account::where('sub_category', 'Beban Penyusutan')->where('name','Beban Penyusutan ' . $fixedAsset['name'])->first();

        $account->update([
            'name' => 'Beban Penyusutan ' . $attributes['name'],
        ]);

        $attributes['detail'] = "Pengadaan Harta Tetap - " . $attributes['name'];
        $attributes['author'] = $request->user()->name;

        $journal = Journal::where('no_ref', $attributes['no_ref'])->first();
        $journal->update($attributes);

        //masukkan data ke buku besar
        //debit
        $ledger = Ledger::where('no_ref', $attributes['no_ref'])->where('credit', 0)->first();
        $ledger->update([
            'account_name' => $account_to_ledger['name'],
            'account_id' => $account_to_ledger['id'],
            'account_code' => $account_to_ledger['code'],
            'no_ref' => $attributes['no_ref'],
            'debit' => $attributes['value'],
            'credit' => 0,
            'date' => $attributes['date'],
            'author' => $attributes['author'],
            'note' => $attributes['detail'],
        ]);

        //credit
        $account = Account::where('id', $request->credit_account['id'])->first();
        $ledger = Ledger::where('no_ref', $attributes['no_ref'])->where('debit', 0)->first();
        //debit
        $ledger->update([
            'account_name' => $account['name'],
            'account_id' => $account['id'],
            'account_code' => $account['code'],
            'no_ref' => $attributes['no_ref'],
            'debit' => 0,
            'credit' => $attributes['value'],
            'date' => $attributes['date'],
            'author' => $attributes['author'],
            'note' => $attributes['detail'],
        ]);

        //cek table cashflow
        $cashFlow = Cashflow::where('no_ref',$attributes['no_ref'])->first();
        if ($cashFlow) {
            $cashFlow->delete();
        }

        //jika debit akun adalah kas maka masukkan ke tabel arus kas dengan 
        if ((int)$account['code'] < 1300001) {
            Cashflow::create([
                'account_id' => $account['id'],
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'account_code' => $account['code'],
                'account_name' => $account['name'],
                'type' => 'investment',
                'debit' => 0,
                'credit' => $attributes['value'],
            ]);
        }

        $fixedAsset->update($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $fixedAsset,
        ]); 
    }

    public function destroy(FixedAsset $fixedAsset)
    {

        // check apabila telah dilakukan penyusutan maka data tidak dihapus
        //ledger Akumulasi Penyusutan
        $condition = 'Akumulasi Penyusutan ' . $fixedAsset['name'];
        $ledger = Ledger::where('account_name', $condition)->first();

        if ($ledger) {
            $message = "Tidak Bisa Dihapus, " . $fixedAsset['name'] . " Telah Digunakan Pada Penyusutan";
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        $fixedAsset->delete();

        //hapus akun
        $accounts = Account::where('name', 'like', '%' . $fixedAsset['name'] . '%')->get();
        if (count($accounts) > 0) {
            foreach ($accounts as $account) {
                $account->delete();
            }
        }

        //hapus journal
        $journal = Journal::where('no_ref', $fixedAsset['no_ref'])->first();
        $journal->delete();

        //ledger
        $ledgers = Ledger::where('no_ref', $fixedAsset['no_ref'])->get();

        //hapus
        if (count($ledgers) > 0) {
            foreach ($ledgers as $ledger) {
                $ledger->delete();
            }
        }

        //cek cashflow
        $cashFlows = CashFlow::where('no_ref', $fixedAsset['no_ref'])->first();
        if ($cashFlows) {
            $cashFlows->delete();
        }

        return response()->json([
            'status' => 'success',
            'data' => $fixedAsset,
        ]); 
    }
}
