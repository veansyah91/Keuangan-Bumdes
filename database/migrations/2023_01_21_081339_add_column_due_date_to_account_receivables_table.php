<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDueDateToAccountReceivablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_receivables', function (Blueprint $table) {
            $table->bigInteger('late')->default(0)->nullable();
            $table->date('due_date')->nullable();
            $table->integer('tenor')->default(1)->nullable();//in month
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
