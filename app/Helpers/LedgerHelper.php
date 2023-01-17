<?php 

namespace App\Helpers;

use App\Models\Ledger;
use App\Models\Businessledger;

class LedgerHelper {

    public static function index($no_ref)
    {
        return Ledger::Where('no_ref', $no_ref)->orderBy('debit', 'desc')->get();
    }

    public static function business($business, $no_ref)
    {
        return Businessledger::where('business_id', $business['id'])->Where('no_ref', $no_ref)->orderBy('debit', 'desc')->get();
    }
}