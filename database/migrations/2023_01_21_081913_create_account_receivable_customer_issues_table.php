<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountReceivableCustomerIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_receivable_customer_issues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_receivable_id');
            $table->foreign('account_receivable_id')->references('id')->on('account_receivables')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('late')->dafault(0);
            $table->date('suspend')->nullable();//menangguhkan
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
        Schema::dropIfExists('account_receivable_customer_issues');
    }
}
