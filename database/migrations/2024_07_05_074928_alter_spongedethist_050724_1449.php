<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSpongedethist0507241449 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sponge_detail_history', function (Blueprint $table) {
            $table->unsignedBigInteger('sponge_detail_id')->after('id');
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
            $table->dropColumn('sponge_detail_id');
        });
    }
}
