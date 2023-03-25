<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToCreditApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_applications', function (Blueprint $table) {
            $table->bigInteger('other_cost')->default(0)->nullable();
            $table->integer('profit')->default(0)->nullable();//per month
            $table->bigInteger('unit_price')->default(0)->nullable();
            $table->bigInteger('downpayment')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_applications', function (Blueprint $table) {
            //
        });
    }
}
