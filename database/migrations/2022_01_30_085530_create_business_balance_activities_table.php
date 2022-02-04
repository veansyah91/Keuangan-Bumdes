<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessBalanceActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_balance_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_balance_id');
            $table->foreign('business_balance_id')->references('id')->on('business_balances')->onUpdate('cascade')->onDelete('cascade');
            $table->string('keterangan');
            $table->date('tanggal');
            $table->bigInteger('uang_masuk')->nullable();
            $table->bigInteger('uang_keluar')->nullable();
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
        Schema::dropIfExists('business_balance_activities');
    }
}
