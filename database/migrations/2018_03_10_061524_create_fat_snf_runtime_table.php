<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFatSnfRuntimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fat_snf_runtime', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyId');
            $table->string('minFatRange');
            $table->string('maxFatRange');
            $table->string('rateIncreseByFatIncrese');
            $table->string('rateIncreseBySnfIncrese');
            $table->string('rateDecreaseByFatDecrease');
            $table->string('rateDecreaseBySnfDecrease');
            $table->string('MidPointFat');
            $table->string('MidPointSnf');
            $table->string('RateByMidPoint');
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
        Schema::dropIfExists('fat_snf_runtime');
    }
}
