<?php 

namespace App\Helpers;

use App\Models\Incomingitem;

class IncomingItemHelper {
    public static function getData($id)
    {
        return Incomingitem::find($id);
    }
}