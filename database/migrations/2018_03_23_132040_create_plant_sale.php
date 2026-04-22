<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlantSale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plant_sale', function (Blueprint $table) {
            $table->increments('id');
            $table->string('plantId');
            $table->string('plantOther')->nullable();
            $table->string('date');    
            $table->string('milkType');   
            $table->string('quantity');
            $table->string('priceQuantity');
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
