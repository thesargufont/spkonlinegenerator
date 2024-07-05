<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSpongedethist0607240016 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sponge_detail_history', function (Blueprint $table) {
            $table->dropColumn('start_at');
            $table->dropColumn('estimated_end');
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
            $table->dateTime('start_at');
            $table->dateTime('estimated_end');
        });
    }
}
