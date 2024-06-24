<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableLocationHists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('location_hists', function (Blueprint $table) {
            $table->unsignedBigInteger('basecamp_id')->nullable()->default(null)->after('location_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('location_hists', function (Blueprint $table) {
            $table->dropColumn('basecamp_id');
        });
    }
}
