<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRevenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revenues', function (Blueprint $table) {
            // $table->unsignedBigInteger('to_account_id');
            // $table->foreign('to_account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
            // $table->unsignedBigInteger('from_account_id');
            // $table->foreign('from_account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
            $table->id();
            $table->string('no_ref');//CI-datenumber
            $table->date('date');
            $table->string('description')->nullable();//automatic (diterima dari)
            $table->bigInteger('value');
            $table->string('contact');
            $table->string('author');
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
        Schema::dropIfExists('revenues');
    }
}
