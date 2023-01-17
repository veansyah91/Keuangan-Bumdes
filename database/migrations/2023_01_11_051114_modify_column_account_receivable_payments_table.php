<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyColumnAccountReceivablePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_receivable_payments', function (Blueprint $table) {
            $table->renameColumn('jumlah_bayar', 'value');
            $table->renameColumn('operator', 'author');
            $table->string('no_ref');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_receivable_payment', function (Blueprint $table) {
            //
        });
    }
}
