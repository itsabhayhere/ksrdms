    <?php

    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;

    class AddOpeningBalanceToUsersTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table('dairy_info', function (Blueprint $table) {
                $table->integer('ledgerId')->nullable();
                $table->string('openingBalance')->nullable();   
                $table->string('openingBalanceType')->nullable();   
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('dairy_info', function (Blueprint $table) {
                //
            });
        }
    }
