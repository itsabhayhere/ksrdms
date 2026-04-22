<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShiftToSalesTable extends Migration
{
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {

            // Add shift column after saleDate
            $table->string('shift')
                  ->nullable()
                  ->after('saleDate');

        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {

            // Drop shift column if rollback
            $table->dropColumn('shift');

        });
    }
}