<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlantRateCardTables extends Migration
{
    public function up()
    {
        /* ── 1. plant_ratecardshort ───────────────────────────────────────
           mirrors: ratecardshort
        ─────────────────────────────────────────────────────────────────── */
        Schema::create('plant_ratecardshort', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyId');
            $table->string('rateCardType')->default('fat');   // 'fat' | 'fat/snf'
            $table->string('rateCardFor')->nullable();         // 'cow' | 'buffalo' | 'both'
            $table->decimal('minFat', 5, 2)->nullable();
            $table->decimal('maxFat', 5, 2)->nullable();
            $table->decimal('minSnf', 5, 2)->nullable();
            $table->decimal('maxSnf', 5, 2)->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        /* ── 2. plant_rangelist ───────────────────────────────────────────
           mirrors: rangelist
        ─────────────────────────────────────────────────────────────────── */
        Schema::create('plant_rangelist', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyId');
            $table->unsignedInteger('rateCardId');
            $table->decimal('mnFat',      5, 2)->nullable();
            $table->decimal('mxFat',      5, 2)->nullable();
            $table->decimal('mnSnf',      5, 2)->nullable();
            $table->decimal('mxSnf',      5, 2)->nullable();
            $table->decimal('rDecFat',    8, 4)->nullable();
            $table->decimal('rDecSnf',    8, 4)->nullable();
            $table->decimal('rIncFat',    8, 4)->nullable();
            $table->decimal('rIncSnf',    8, 4)->nullable();
            $table->decimal('rAvgFatSnf', 8, 4)->nullable();
            $table->decimal('avgFat',     5, 2)->nullable();
            $table->timestamps();
        });

        /* ── 3. plant_fat_snf_ratecard ────────────────────────────────────
           mirrors: fat_snf_ratecard
        ─────────────────────────────────────────────────────────────────── */
        Schema::create('plant_fat_snf_ratecard', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dairyId');
            $table->unsignedInteger('rateCardShortId');
            $table->unsignedInteger('rangeListId');
            $table->decimal('fatRange', 5, 2);
            $table->decimal('snfRange', 5, 2)->nullable();
            $table->decimal('amount',   10, 4);
            $table->timestamps();
        });

        /* ── 4. Add applied-card columns to dairy_info ───────────────────
           Using nullable string so it works regardless of dairy_info's
           primary key type. No ->after() — avoids the "Method id does not
           exist" error on older Laravel/DB driver combinations.
        ─────────────────────────────────────────────────────────────────── */
        Schema::table('dairy_info', function (Blueprint $table) {
            $table->string('plantRateCardIdForCow')->nullable();
            $table->string('plantRateCardTypeForCow')->nullable();
            $table->string('plantRateCardIdForBuffalo')->nullable();
            $table->string('plantRateCardTypeForBuffalo')->nullable();
        });
    }

    public function down()
    {
        Schema::table('dairy_info', function (Blueprint $table) {
            $table->dropColumn([
                'plantRateCardIdForCow',
                'plantRateCardTypeForCow',
                'plantRateCardIdForBuffalo',
                'plantRateCardTypeForBuffalo',
            ]);
        });

        Schema::dropIfExists('plant_fat_snf_ratecard');
        Schema::dropIfExists('plant_rangelist');
        Schema::dropIfExists('plant_ratecardshort');
    }
}