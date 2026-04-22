<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FatSnfRatecard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fat_snf_Ratecard', function (Blueprint $table) {
            $table->increments('id');
            $table->time('dairyId');
            $table->time('fatRange');
            $table->string('snfRange');   
            $table->string('amount');   
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
