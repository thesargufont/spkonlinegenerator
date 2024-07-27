<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSpongeDetails270720241006 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sponge_details', function (Blueprint $table) {
            $table->dropColumn('reporter_location');
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
            $table->string('reporter_location','50')->default('')->after('cr_number');
        });
    }
}
