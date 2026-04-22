<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyCode');
            $table->string('memberCode');
            $table->string('password');
            $table->string('name');
            $table->string('fatherName');
            $table->string('gender');
            $table->string('email');
            $table->string('mobileNo');
            $table->string('phoneNo');
            $table->string('address');
            $table->string('village');
            $table->string('city');
            $table->string('district');
            $table->string('state');
            $table->string('country');
            $table->string('pin');
            $table->string('phonePinCode');
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
        Schema::dropIfExists('members');
    }
}
