<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSpongeDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sponge_details', function (Blueprint $table) {
            $table->dropUnique('table_1_unique');
            //$table->unique(['wo_number_id', 'reporter_location', 'device_id', 'disturbance_category'], 'table_1_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sponge_details', function (Blueprint $table) {
            $table->unique(['reporter_location', 'device_id'], 'table_1_unique');
        });
    }
}
