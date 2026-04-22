<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MemberPersonalInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    //memberPersonal
    {
         Schema::create('member_personal_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dairyId');
            $table->string('status');
            $table->string('memberPersonalCode');
            $table->string('memberPersonalregisterDate');
            $table->string('memberPersonalName');
            $table->string('memberPersonalFatherName');
            $table->string('memberPersonalGender');
            $table->string('memberPersonalEmail');
            $table->string('memberPersonalAadarNumber');
            $table->string('memberPersonalMobileNumber');
            $table->string('memberPersonalAddress');
            $table->string('memberPersonalState');
            $table->string('memberPersonalCity');
            $table->string('memberPersonalDistrictVillage');
            $table->string('memberPersonalMobilePincode');
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
