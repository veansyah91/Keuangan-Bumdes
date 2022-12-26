<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessledgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businessledgers', function (Blueprint $table) {
            $table->id();
            $table->string('author')->nullable();
            $table->string('note')->nullable();
            $table->string('account_name');
            $table->unsignedBigInteger('account_id');
            $table->foreign('account_id')->references('id')->on('businessaccounts')->onUpdate('cascade')->onDelete('cascade');
            $table->string('no_ref');
            $table->bigInteger('debit')->default(0);
            $table->bigInteger('credit')->default(0);
            $table->date('date');
            $table->string('description')->nullable();
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
        Schema::dropIfExists('businessledgers');
    }
}
