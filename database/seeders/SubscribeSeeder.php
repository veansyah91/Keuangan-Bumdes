<?php

namespace Database\Seeders;

use App\Models\Subscribe;
use Illuminate\Database\Seeder;

class SubscribeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = date('Ymd');
        $attributes['no_ref'] = 'BP' . $date . strval(rand(100000,1000000));
        
        $attributes['due_date'] = date('Y-m-d', strtotime('+1week', strtotime(date('Y-m-d'))));

        Subscribe::create($attributes);
    }
}
