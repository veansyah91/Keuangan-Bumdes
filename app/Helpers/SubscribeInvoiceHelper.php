<?php

namespace App\Helpers;

use App\Models\SubscribeInvoice;

class SubscribeInvoiceHelper {
    public static function invoice()
    {
        return SubscribeInvoice::first();
    }
}