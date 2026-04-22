<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyId');
            $table->string('status');
            $table->string('customerCode');
            $table->string('customerName');
            $table->string('gender');
            $table->string('customerEmail');
            $table->string('customerMobileNumber');
            $table->string('customerAddress');
            $table->string('customerState');
            $table->string('customerCity');
            $table->string('customerVillageDistrict');
            $table->string('customerPincode');
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
        Schema::dropIfExists('_customer');
    }
}
