<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessaccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businessaccounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->boolean('is_cash')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('sub_classification_account_id')->nullable();
            $table->foreign('sub_classification_account_id')->references('id')->on('sub_classification_accounts')->onUpdate('cascade')->onDelete('cascade');
            $table->string('sub_category');
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
        Schema::dropIfExists('businessaccounts');
    }
}
