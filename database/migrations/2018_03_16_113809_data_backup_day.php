<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DataBackupDay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //time
        Schema::create('data_backup_day', function (Blueprint $table) {
            $table->increments('id');
            $table->time('startTime');
            $table->time('endTime');
            $table->string('monday')->nullable();   
            $table->string('tuesday')->nullable();   
            $table->string('wednedsay')->nullable();   
            $table->string('thursday')->nullable();   
            $table->string('friday')->nullable();   
            $table->string('sterday')->nullable();   
            $table->string('sunday')->nullable();   
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
