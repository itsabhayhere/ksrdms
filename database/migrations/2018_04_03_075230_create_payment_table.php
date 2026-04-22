<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
              $table->increments('id');
            $table->string('dairyId');
            $table->string('status');   
            $table->string('ledgerId');
            $table->string('partyName');
            $table->date('paymentDate'); 
            $table->time('paymentTime'); 
            $table->string('paymentMode');  
            $table->string('paymentAmount');
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
        Schema::dropIfExists('payment');
    }
}
