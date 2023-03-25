<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCreditApplicationIdToAccountReceivablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_receivables', function (Blueprint $table) {
            $table->unsignedBigInteger('credit_application_id')->nullable();
            $table->foreign('credit_application_id')->references('id')->on('credit_applications')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_receivables', function (Blueprint $table) {
            //
        });
    }
}
