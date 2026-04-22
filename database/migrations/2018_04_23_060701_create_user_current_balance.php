<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCurrentBalance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('user_current_balance', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ledgerId')->nullable();   
            $table->string('userId')->nullable();   
            $table->string('userType')->nullable();
            $table->string('openingBalance')->nullable();   
            $table->string('openingBalanceType')->nullable();
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
