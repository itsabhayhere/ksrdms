<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalSale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('local_sale', function (Blueprint $table) {
            $table->increments('id');
            $table->string('paymentMode');
            $table->string('paymentModeOther')->nullable();
            $table->string('product');   
            $table->string('productOther')->nullable();  
            $table->string('date');   
            $table->string('unit');   
            $table->string('unitSpecify')->nullable();
            $table->string('quantity');
            $table->string('PricePerUnit');
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
        //
    }
}
