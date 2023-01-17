<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Models\Businessjournal;
use App\Models\Businesscashflow;
use App\Models\Businessfixedasset;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class BusinessFixedAssetController extends Controller
{
    public function newNoRefFixedAsset($no_ref_request, $no_ref_contact){
        $split_contact_ref_no = explode("-", $no_ref_contact);
        $old_ref_no = (int)$split_contact_ref_no[1];
        $new_ref_no = 1000000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }
    
    public function noRefFixedAssetRecomendation(Business $business){
        $fix_ref_no = '';

        $fixedAsset = Businessfixedasset::where('business_id', $business['id'])->orderBy('id', 'desc')->first();

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

    public function getData(Business $business){
        $fixedAsset = Businessfixedasset::where('business_id', $business['id'])->filter(request(['search']))
                                ->orderBy('is_active', 'desc')
                                ->orderBy('date', 'desc')
                                ->orderBy('id', 'desc')
                                ->paginate(50);

        return response()->json([
            'status' => 'success',
            'data' => $fixedAsset,
        ]); 
    }

    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        
        return view('business.fixed-asset.index', compact('business'));
    }

    public function store(Request $request, Business $business)
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
        $attributes['business_id'] = $business['id'];

        $fixedAsset = Businessfixedasset::create($attributes);

        //buat akun Harta Tetap, Penyusutan Harta Tetap, dan Beban Penyusutan Harta Tetap
        //asset
        $account = Businessaccount::where('business_id', $business['id'])->where('sub_category', 'Harta Tetap Berwujud')->orderBy('id', 'desc')->first();
        
        $account_to_ledger = Businessaccount::create([
            'name' => $attributes['name'],
            'code' => (int)$account['code'] + 1,
            'is_active' => true,
            'is_cash' => false,
            'sub_category' => 'Harta Tetap Berwujud',
            'sub_classification_account_id' => $account['sub_classification_account_id'],
            'business_id' => $business['id']
        ]);

        //Penyusutan Harta Tetap
        $account = Businessaccount::where('business_id', $business['id'])->where('sub_category', 'Akumulasi Penyusutan Harta Tetap')->orderBy('id', 'desc')->first();

        Businessaccount::create([
            'name' => 'Akumulasi Penyusutan ' . $attributes['name'],
            'code' => (int)$account['code'] + 1,
            'is_active' => true,
            'is_cash' => false,
            'sub_category' => 'Akumulasi Penyusutan Harta Tetap',
            'sub_classification_account_id' => $account['sub_classification_account_id'],
            'business_id' => $business['id']
        ]);

         //Beban Penyusutan Harta Tetap
         $account = Businessaccount::where('business_id', $business['id'])->where('sub_category', 'Beban Penyusutan')->orderBy('id', 'desc')->first();

         Businessaccount::create([
             'name' => 'Beban Penyusutan ' . $attributes['name'],
             'code' => (int)$account['code'] + 1,
             'is_active' => true,
             'is_cash' => false,
             'sub_category' => 'Beban Penyusutan',
             'sub_classification_account_id' => $account['sub_classification_account_id'],
             'business_id' => $business['id']
         ]);

        //masukkan data ke jurnal 
        $journal = Businessjournal::create($attributes);

        //masukkan data ke buku besar
        //debit
        Businessledger::create([
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
            'business_id' => $business['id']
        ]);

        //credit
        $account = Businessaccount::where('business_id', $business['id'])->where('id', $request->credit_account['id'])->first();

        //debit
        Businessledger::create([
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
            'business_id' => $business['id']
        ]);

        //jika debit akun adalah kas maka masukkan ke tabel arus kas dengan 
        if ((int)$account['code'] < 1300001) {
            Businesscashflow::create([
                'account_id' => $account_to_ledger['id'],
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'account_code' => $account_to_ledger['code'],
                'account_name' => $account_to_ledger['name'],
                'type' => 'investment',
                'debit' => 0,
                'credit' => $attributes['value'],
                'business_id' => $business['id']
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]); 
    }

    public function show(Business $business, Businessfixedasset $businessfixedasset)
    {
        $ledger = Businessledger::where('business_id', $business['id'])->where('no_ref', $businessfixedasset['no_ref'])->where('credit', '>', 0)->first();

        $account = Businessaccount::where('business_id', $business['id'])->where('name', $businessfixedasset['name'])->first();

        $businessfixedasset['debit_account'] =[
            "id" => $account['id'],
            "code" => $account['code'],
            "name" => $account['name']
        ];

        $businessfixedasset['credit_account'] =[
            "id" => $ledger['account_id'],
            "code" => $ledger['account_code'],
            "name" => $ledger['account_name']
        ];

        $name_account = 'Akumulasi Penyusutan ' . $businessfixedasset['name'];
        $account = Businessaccount::where('business_id', $business['id'])->where('name', $name_account)->first();
        $ledgers = Businessledger::where('business_id', $business['id'])->where('account_id', $account['id'])->get();

        $businessfixedasset['depreciates'] = $ledgers;

        return response()->json([
            'status' => 'success',
            'data' => $businessfixedasset,
        ]); 
    }

    public function edit(Business $business, Businessfixedasset $businessfixedasset)
    {
        $ledger = Businessledger::where('business_id', $business['id'])->where('no_ref', $businessfixedasset['no_ref'])->where('credit', '>', 0)->first();

        //ledger Akumulasi Penyusutan
        $condition = 'Akumulasi Penyusutan ' . $businessfixedasset['name'];
        $depreciate = Businessledger::where('business_id', $business['id'])->where('account_name', $condition)->first();

        $businessfixedasset['credit_account'] =[
            "id" => $ledger['account_id'],
            "name" => $ledger['account_name']
        ];
        $businessfixedasset["is_depreciate"] = $depreciate ? true : false;
        return response()->json([
            'status' => 'success',
            'data' => $businessfixedasset,
        ]); 
    }


    public function update(Request $request,Business $business, Businessfixedasset $businessfixedasset)
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
        $account_to_ledger = Businessaccount::where('business_id', $business['id'])->where('sub_category', 'Harta Tetap Berwujud')->where('name', $businessfixedasset['name'])->first();
        
        $account_to_ledger->update([
            'name' => $attributes['name'],
        ]);

        //Penyusutan Harta Tetap
        $account = Businessaccount::where('business_id', $business['id'])->where('sub_category', 'Akumulasi Penyusutan Harta Tetap')->where('name', 'Akumulasi Penyusutan ' . $businessfixedasset['name'])->first();
        
        $account->update([
            'name' => 'Akumulasi Penyusutan ' . $attributes['name'],
        ]);

        //Beban Penyusutan Harta Tetap
        $account = Businessaccount::where('business_id', $business['id'])->where('sub_category', 'Beban Penyusutan')->where('name','Beban Penyusutan ' . $businessfixedasset['name'])->first();

        $account->update([
            'name' => 'Beban Penyusutan ' . $attributes['name'],
        ]);

        $attributes['detail'] = "Pengadaan Harta Tetap - " . $attributes['name'];
        $attributes['author'] = $request->user()->name;

        $journal = Businessjournal::where('business_id', $business['id'])->where('no_ref', $attributes['no_ref'])->first();
        $journal->update($attributes);

        //masukkan data ke buku besar
        //debit
        $ledger = Businessledger::where('business_id', $business['id'])->where('no_ref', $attributes['no_ref'])->where('credit', 0)->first();
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
        $account = Businessaccount::where('business_id', $business['id'])->where('id', $request->credit_account['id'])->first();
        $ledger = Businessledger::where('business_id', $business['id'])->where('no_ref', $attributes['no_ref'])->where('debit', 0)->first();
        
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
        $cashFlow = Businesscashflow::where('no_ref',$attributes['no_ref'])->first();
        if ($cashFlow) {
            $cashFlow->delete();
        }

        //jika debit akun adalah kas maka masukkan ke tabel arus kas dengan 
        if ((int)$account['code'] < 1300001) {
            Businesscashflow::create([
                'account_id' => $account_to_ledger['id'],
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'account_code' => $account_to_ledger['code'],
                'account_name' => $account_to_ledger['name'],
                'type' => 'investment',
                'debit' => 0,
                'credit' => $attributes['value'],
                'business_id' => $business['id']
            ]);
        }

        $businessfixedasset->update($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $businessfixedasset,
        ]); 
    }

    public function destroy(Business $business, Businessfixedasset $businessfixedasset)
    {

        // check apabila telah dilakukan penyusutan maka data tidak dihapus
        //ledger Akumulasi Penyusutan
        $condition = 'Akumulasi Penyusutan ' . $businessfixedasset['name'];
        $ledger = Businessledger::where('business_id', $business['id'])->where('account_name', $condition)->first();

        if ($ledger) {
            $message = "Tidak Bisa Dihapus, " . $businessfixedasset['name'] . " Telah Digunakan Pada Penyusutan";
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        $businessfixedasset->delete();

        //hapus akun
        $accounts = Businessaccount::where('business_id', $business['id'])->where('name', 'like', '%' . $businessfixedasset['name'] . '%')->get();
        if (count($accounts) > 0) {
            foreach ($accounts as $account) {
                $account->delete();
            }
        }

        //hapus journal
        $journal = Businessjournal::where('no_ref', $businessfixedasset['no_ref'])->first();
        $journal->delete();

        //ledger
        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $businessfixedasset['no_ref'])->get();

        //hapus
        if (count($ledgers) > 0) {
            foreach ($ledgers as $ledger) {
                $ledger->delete();
            }
        }

        //cek cashflow
        $cashFlows = BusinesscashFlow::where('no_ref', $businessfixedasset['no_ref'])->first();
        if ($cashFlows) {
            $cashFlows->delete();
        }

        return response()->json([
            'status' => 'success',
            'data' => $businessfixedasset,
        ]); 
    }
}
