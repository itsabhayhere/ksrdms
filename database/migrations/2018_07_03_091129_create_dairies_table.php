<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDairiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dairies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyCode');
            $table->string('name');
            $table->string('OwnerName');
            $table->string('mobileNo');
            $table->string('phoneNo');
            $table->string('email');
            $table->string('address');
            $table->string('city');
            $table->string('district');
            $table->string('state');
            $table->string('pin');
            $table->string('country');
            $table->string('password');
            $table->string('reteCard');
            $table->dateTime('isActivated');

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
        Schema::dropIfExists('dairies');
    }
}
