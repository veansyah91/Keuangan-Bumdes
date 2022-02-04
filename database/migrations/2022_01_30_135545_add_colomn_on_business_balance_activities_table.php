<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColomnOnBusinessBalanceActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_balance_activities', function (Blueprint $table) {
            $table->boolean('bumdes')->nullable();
            $table->unsignedBigInteger('business_expense_id')->nullable();
            $table->foreign('business_expense_id')->references('id')->on('business_expenses')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('closing_income_activity_id')->nullable();
            $table->foreign('closing_income_activity_id')->references('id')->on('closing_income_activities')->onUpdate('cascade')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_balance_activities', function (Blueprint $table) {
            //
        });
    }
}
