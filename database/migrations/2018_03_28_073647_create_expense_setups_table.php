<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseSetupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   //date
        Schema::create('expense_setups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyId');   
            $table->string('status');   
            $table->string('ledgerName');   
            $table->string('partyName');   
            $table->date('date');   
            $table->string('time');   
            $table->string('expenseType');   
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
        Schema::dropIfExists('expense_setups');
    }
}
