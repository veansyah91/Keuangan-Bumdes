<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinesscashflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesscashflows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->foreign('account_id')->references('id')->on('businessaccounts')->onUpdate('cascade')->onDelete('cascade');
            $table->string('no_ref');
            $table->date('date');
            $table->string('account_code');
            $table->string('account_name');
            $table->enum('type',['operation', 'investment','finance']);
            $table->bigInteger('debit')->default(0);
            $table->bigInteger('credit')->default(0);
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('businesses')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('businesscashflows');
    }
}
