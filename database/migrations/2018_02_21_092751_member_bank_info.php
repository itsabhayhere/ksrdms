<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MemberBankInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('member_personal_bank_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('memberPersonalUserId');
            $table->string('memberPersonalBankName');
            $table->string('memberPersonalAccountName');
            $table->string('memberPersonalIfsc');
            $table->string('memberPersonalBranchCode');
            $table->string('openingBalance');
            $table->string('openingBalanceType');
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
