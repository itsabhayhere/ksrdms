<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyId');
            $table->string('status');
            $table->string('memberCode');
            $table->string('memberName');
            $table->dateTime('date');
            $table->string('milkType');
            $table->string('milkQuality');
            $table->string('rateCardType');
            $table->string('fat');
            $table->string('snf');
            $table->string('amount');
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
        Schema::dropIfExists('daily_transactions');
    }
}
