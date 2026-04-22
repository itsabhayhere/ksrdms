<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DairyPropritorInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //dairyPropritorInfo
        Schema::create('dairy_propritor_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dairyId');
            $table->string('dairyPropritorName');
            $table->string('PropritorMobile');
            $table->string('dairyPropritorAddress');
            $table->string('dairyPropritorEmail');
            $table->string('dairyPropritorState');
            $table->string('dairyPropritorCity');
            $table->string('dairyPropritorDistrict');
            $table->string('dairyPropritorPincode');
            $table->rememberToken();
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
        //
    }
}
