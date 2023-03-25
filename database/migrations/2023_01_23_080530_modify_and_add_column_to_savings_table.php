<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAndAddColumnToSavingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('savings', function (Blueprint $table) {
            $table->unsignedBigInteger('saving_account_id');
            $table->foreign('saving_account_id')->references('id')->on('saving_accounts')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('value')->default(0)->change();

            $table->renameColumn('value', 'credit');
            $table->bigInteger('debit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('savings', function (Blueprint $table) {
            //
        });
    }
}
