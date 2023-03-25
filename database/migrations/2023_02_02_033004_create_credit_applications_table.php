<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('credit_applications', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->nullable();//approved or not approved
            $table->date('due_date');//jatuh tempo
            $table->integer('tenor');//
            $table->bigInteger('value');
            $table->string('author');
            $table->string('no_ref');
            $table->string('contact_name')->nullable();
            $table->date('date');
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('businesses')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->foreign('contact_id')->references('id')->on('contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
            
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
        Schema::dropIfExists('credit_applications');
    }
}
