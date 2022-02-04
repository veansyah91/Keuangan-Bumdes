<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountReceivablePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_receivable_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_receivable_id')->nullable();
            $table->foreign('account_receivable_id')->references('id')->on('account_receivables')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('jumlah_bayar');
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
        Schema::dropIfExists('account_receivable_payments');
    }
}
