<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribeInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribe_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('no_ref');
            $table->bigInteger('value');
            $table->string('package')->default('yearly');//monthly, yearly
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_waiting')->default(false);
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscribe_invoices');
    }
}
