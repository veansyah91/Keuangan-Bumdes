<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ledger;
use App\Models\Account;
use App\Models\Journal;
use App\Models\FixedAsset;
use Illuminate\Http\Request;

class FixedAssetDepreciationController extends Controller
{
    public function newNoRefFixedAsset($no_ref_request, $no_ref_contact){
        $split_contact_ref_no = explode("-", $no_ref_contact);
        $old_ref_no = (int)$split_contact_ref_no[1];
        $new_ref_no = 1000000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }

    public function __invoke(Request $request)
    {

        $fixedAssets = FixedAsset::whereIsActive(true)->get();

        if (count($fixedAssets) > 0) {
            foreach ($fixedAssets as $fixedAsset) {
                $depreciatePerMonth = ($fixedAsset['value'] - $fixedAsset['salvage']) / $fixedAsset['useful_life'];

                //cek apakah penyusutan sudah mencapai nilai fix asset
                $sumDebitLedgers = Ledger::whereAccountName('Akumulasi Penyusutan ' . $fixedAsset['name'])->get()->sum('debit');

                if ($sumDebitLedgers < $fixedAsset['value'] - $fixedAsset['salvage']) {
                    
                    //cari selisih
                    $differentYear =  (int)Carbon::now()->format('y') - (int)Carbon::parse($fixedAsset['date'])->format('y');
                    $differentMonth = (int)Carbon::now()->format('m') - (int)Carbon::parse($fixedAsset['date'])->format('m');
                    $different = $differentMonth + (12 * $differentYear) + 1;
                    $startMonth = $fixedAsset['date'];

                    //cek bulan terakhir dilakukan penyusutan
                    $ledger = Ledger::whereAccountName('Akumulasi Penyusutan ' . $fixedAsset['name'])->orderBy('date', 'desc')->first();

                    for ($i=0; $i < $different; $i++) { 

                        $date = strtotime(date('Y-m-d', strtotime($startMonth. ' + ' . $i . ' months')));

                        $last_date = date("Y-m-t", $date);

                        $fix_ref_no = '';

                        $symbol = 'FAD';

                        $journal = Journal::where('no_ref', 'like', $symbol . '%')->orderBy('id', 'desc')->first();

                        if($journal){
                            $fix_ref_no = $this->newNoRefFixedAsset('FAD', $journal->no_ref);
                        }else{
                            $fix_ref_no = 'FAD-000001';
                        }

                        //journal
                        Journal::updateOrCreate(
                            [
                                'date' => $last_date,
                            ],
                            [
                                'no_ref' => $fix_ref_no,
                                'desc' => 'Penyusutan Harta Tetap',
                                'value' => $depreciatePerMonth,
                                'detail' => 'Penyusutan Harta Tetap - ' .  $fixedAsset['name'],
                                'author' => $request->user()->name,
                                'source' => 'Penyusutan Harta Tetap',
                            ]);

                        //buku besar
                        //akumulasi penyusutan
                        $account = Account::whereName('Akumulasi Penyusutan ' . $fixedAsset['name'])->first();
                        
                        Ledger::updateOrCreate(
                            [
                                'date' => $last_date,
                                'account_name' => $account['name'],
                                'account_id' => $account['id'],
                                'account_code' => $account['code'],
                            ],
                            [
                                'account_id' => $account['id'],
                                'account_code' => $account['code'],
                                'no_ref' => $fix_ref_no,
                                'debit' => 0,
                                'credit' => $depreciatePerMonth,
                                'author' => $request->user()->name,
                                'description' => 'Akumulasi Penyusutan Harta Tetap',
                                'note' => "Akumulasi Penyusutan - " .  $fixedAsset['name'],
                            ]);

                        //beban akumulasi penyusutan
                        $account = Account::whereName('Beban Penyusutan ' . $fixedAsset['name'])->first();

                        Ledger::updateOrCreate(
                            [
                                'date' => $last_date,
                                'account_name' => $account['name'],
                                'account_id' => $account['id'],
                                'account_code' => $account['code'],
                            ],
                            [
                                'no_ref' => $fix_ref_no,
                                'debit' => $depreciatePerMonth,
                                'credit' => 0,
                                'author' => $request->user()->name,
                                'description' => 'Beban Penyusutan Harta Tetap',
                                'note' => "Beban Penyusutan - " .  $fixedAsset['name'],
                            ]);
                    }
                }
            }
        }

        return response()->json([
            'status' => 'success',
        ]); 
    }
}
