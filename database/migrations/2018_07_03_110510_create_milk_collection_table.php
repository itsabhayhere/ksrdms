<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMilkCollectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('milkCollection', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyCode');
            $table->string('memberCode');
            $table->double('quantity', 8, 2);
            $table->double('fat', 8, 2);
            $table->double('snf', 8, 2);
            $table->double('amount', 10,2);
            $table->string('entryType');
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
        Schema::dropIfExists('milkCollection');
    }
}
