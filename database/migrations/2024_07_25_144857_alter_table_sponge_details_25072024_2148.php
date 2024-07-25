<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSpongeDetails250720242148 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sponge_details', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->after('cr_number')->nullable()->defaultValue(null);
            $table->unique(['wo_number_id', 'cr_number', 'location_id', 'device_id', 'disturbance_category'],'sponge_detail_1_unique');
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
            $table->dropColumn('location_id');
            $table->dropUnique('sponge_detail_1_unique');
        });
    }
}
