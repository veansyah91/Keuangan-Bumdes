<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColomnOnIdentityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('identities', function (Blueprint $table) {
            $table->string('nama_bumdes');
            $table->string('no_hp');
            $table->string('kode_pos');
            $table->string('email');
            $table->string('logo_usaha');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('identities', function (Blueprint $table) {
            //
        });
    }
}
