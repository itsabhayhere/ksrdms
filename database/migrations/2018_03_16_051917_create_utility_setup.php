<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUtilitySetup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utility_setup', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyId');
            $table->string('status');
            $table->string('machinType');
            $table->string('communicationPort');
            $table->string('maxSpeed');
            $table->string('echo');
            $table->string('connectionPerferenceDataBits');
            $table->string('connectionPerferenceParity');
            $table->string('connectionPerferenceStopBits');
            $table->string('flowControl');
            $table->string('weightMode')->nullable();
            $table->string('weightMode_auto_tare')->nullable();
            $table->string('weightMode_no_training')->nullable();
            $table->string('weightMode_weight_in_doublke_decimal')->nullable();
            $table->string('weightMode_write_in')->nullable();
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
