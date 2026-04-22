<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DairyInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dairy_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('createBySuperAdmin');
            $table->string('society_code');
            $table->string('society_name');
            $table->string('dairyAddress');
            $table->string('state');
            $table->string('city');
            $table->string('district');
            $table->string('pincode');
            $table->string('status');
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
