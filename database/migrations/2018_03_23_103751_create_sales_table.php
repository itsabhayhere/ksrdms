<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()   
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyId');
            $table->string('status');   
            $table->string('partyName');
            $table->string('productType');
            $table->string('otherproductType')->nullable();
            $table->string('milkType')->nullable();
            $table->string('productQuantity');
            $table->string('unit');
            $table->string('otherUnit')->nullable();;
            $table->string('productPricePerUnit');
            $table->date('saleDate');
            $table->string('ledgerId');
            $table->string('saleType');
            $table->float('amount');
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
        Schema::dropIfExists('sales');
    }
}
