<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToAccountReceivablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_receivables', function (Blueprint $table) {
            $table->unsignedBigInteger('debt_submission_id')->nullable();
            $table->foreign('debt_submission_id')->references('id')->on('debt_submissions')->onUpdate('cascade')->onDelete('cascade');
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
