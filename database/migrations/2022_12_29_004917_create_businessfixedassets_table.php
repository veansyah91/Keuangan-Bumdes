<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessfixedassetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businessfixedassets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('businesses')->onUpdate('cascade')->onDelete('cascade');
            $table->date('date'); //acquisition
            $table->string('no_ref');
            $table->string('name');
            $table->string('value'); //acquired value
            $table->string('salvage'); //residu
            $table->integer('useful_life')->default(0); //masa pemakaian
            $table->boolean('is_active');
            $table->string('author');
            $table->enum('method',['garis lurus', 'saldo menurun'])->default('garis lurus');
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
        Schema::dropIfExists('businessfixedassets');
    }
}
