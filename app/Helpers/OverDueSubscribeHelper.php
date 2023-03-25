<?php

namespace App\Helpers;

use App\Models\Subscribe;


class OverDueSubscribeHelper {
    public static function overDue()
    {
        $subscribe = Subscribe::first();
        
        $now = Date('Y-m-d');

        $different = date_diff(date_create($now), date_create($subscribe['due_date']));

        $subscribe['is_over_due'] = $subscribe['due_date'] > $now ? false : true;
        $subscribe['different'] = $subscribe['due_date'] <= $now ? 0 : $different->days;

        return $subscribe;
    }
}