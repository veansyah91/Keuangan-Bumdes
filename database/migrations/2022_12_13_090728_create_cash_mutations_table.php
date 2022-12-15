<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashMutationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_mutations', function (Blueprint $table) {
            $table->id();
            $table->string('no_ref');//CI-datenumber
            $table->date('date');
            $table->string('description')->nullable();//automatic (diterima dari:, ke:)
            $table->bigInteger('value');
            $table->string('author');
            $table->string('detail')->nullable();
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
        Schema::dropIfExists('cash_mutations');
    }
}
