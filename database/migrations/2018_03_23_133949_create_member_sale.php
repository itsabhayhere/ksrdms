<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberSale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('member_sale', function (Blueprint $table) {
            $table->increments('id');
            $table->string('memberCode');
            $table->string('memberName');
            $table->string('date');   
            $table->string('product');   
            $table->string('productOther')->nullable();  
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
