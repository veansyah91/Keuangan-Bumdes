<?php 

namespace App\Helpers;

use App\Models\BusinessUser;

class BusinessUserHelper {
    public static function index($business, $user)
    {
        return $businessUser = BusinessUser::where('business_id', $business)->where('user_id', $user)->first();
    }

}