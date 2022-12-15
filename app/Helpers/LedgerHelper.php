<?php 

namespace App\Helpers;

use App\Models\Ledger;

class LedgerHelper {

    public static function index($no_ref)
    {
        return Ledger::Where('no_ref', $no_ref)->orderBy('debit', 'desc')->get();
    }
}