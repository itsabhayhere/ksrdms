<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MemberOtherInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_other_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('memberId');
            $table->string('milkeType');
            $table->string('alert_print_slip');
            $table->string('alert_sms');
            $table->string('alert_email');
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
