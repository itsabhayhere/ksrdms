<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseSetupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_setups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyId');   
            $table->string('status');   
            $table->string('ledgerId');   
            $table->string('partyName');   
            $table->date('date');   
            $table->string('time');   
            $table->string('itemsPurchased');   
            $table->string('unit');   
            $table->string('quantity');   
            $table->string('PricePerUnit');   
            $table->string('purchaseType');   
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
        Schema::dropIfExists('purchase_setups');
    }
}
