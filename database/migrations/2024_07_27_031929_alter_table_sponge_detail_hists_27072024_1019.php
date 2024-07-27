<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSpongeDetailHists270720241019 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sponge_detail_history', function (Blueprint $table) {
            $table->dropColumn('reporter_location');
            $table->unsignedBigInteger('location_id')->after('cr_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sponge_detail_history', function (Blueprint $table) {
            $table->string('reporter_location','50')->default('')->after('cr_number');
            $table->dropColumn('location_id');
        });
    }
}
