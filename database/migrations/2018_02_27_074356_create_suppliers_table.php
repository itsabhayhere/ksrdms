<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyId');
            $table->string('status');
            $table->string('supplierCode');
            $table->string('supplierFirmName');
            $table->string('supplierPersonName');
            $table->string('supplierEmail');
            $table->string('gender');
            $table->string('supplierMobileNumber');
            $table->string('supplierGstin');
            $table->string('supplierAddress');
            $table->string('supplierState');
            $table->string('supplierCity');
            $table->string('supplierVillageDistrict');
            $table->string('supplierPincode');
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
        Schema::dropIfExists('suppliers');
    }
}
