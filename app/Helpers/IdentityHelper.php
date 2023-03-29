<?php 

namespace App\Helpers;

use App\Models\Identity;

class IdentityHelper {
    public static function getDesa()
    {
        $identity = Identity::first();

        return $identity ? $identity['nama_bumdes'] : 'BUMDes';
    }
}