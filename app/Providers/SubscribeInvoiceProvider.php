<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SubscribeInvoiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path() . '/Helpers/SubscribeInvoiceHelper.php';
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
