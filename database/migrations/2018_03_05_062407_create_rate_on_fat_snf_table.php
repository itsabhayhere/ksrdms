<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRateOnFatSnfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rate_on_fat_snf', function (Blueprint $table) {
            $table->increments('id');
            $table->string('range');
            $table->string('minFat');
            $table->string('maxFat');
            $table->string('rateIncreseByFatIncrese');
            $table->string('rateIncreseBySnfIncrese');
            $table->string('rateDecreaseByFatDecrease');
            $table->string('rateDecreaseBySnfDecrease');
            $table->string('MidPointFat');
            $table->string('MidPointSnf');
            $table->string('RateByMidPoint');
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
        Schema::dropIfExists('rate_on_fat_snf');
    }
}
