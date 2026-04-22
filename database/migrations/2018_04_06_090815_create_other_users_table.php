<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtherUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyId');
            $table->string('status');
            $table->string('roleId')->nullable();
            $table->string('userName');
            $table->string('fatherName');
            $table->string('aadharNumber');
            $table->string('userEmail');    
            $table->string('gender');
            $table->string('mobileNumber');
            $table->string('address');
            $table->string('state');
            $table->string('city');
            $table->string('villageDistrict');
            $table->string('pincode');
            $table->string('menuId')->nullable();
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
        Schema::dropIfExists('other_users');
    }
}
