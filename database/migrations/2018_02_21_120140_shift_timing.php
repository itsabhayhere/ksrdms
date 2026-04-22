<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ShiftTiming extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Shift_timing', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shiftDairyId');
            $table->time('morningShiftStartTime');
            $table->time('morningShiftEndtime');
            $table->time('eveningShiftStartTime');
            $table->time('eveningShiftEndtime');
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
