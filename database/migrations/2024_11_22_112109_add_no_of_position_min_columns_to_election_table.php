<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoOfPositionMinColumnsToElectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('election', function (Blueprint $table) {
            $table->string('min_no_of_position_mm')->default('၀')->nullable();
            $table->string('min_no_of_position_en')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('election', function (Blueprint $table) {
            $table->dropColumn('min_no_of_position_mm');
            $table->dropColumn('min_no_of_position_en');
        });
    }
}
