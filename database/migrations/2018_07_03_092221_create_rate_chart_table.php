<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRateChartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rateChart', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyCode');
            $table->double('fat',8,1);
            $table->double('snf',8,1);
            $table->double('rate',8,2);
            $table->string('chartType');
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
        Schema::dropIfExists('rateChart');
    }
}
