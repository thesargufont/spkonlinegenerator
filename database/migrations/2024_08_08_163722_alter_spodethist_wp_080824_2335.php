<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSpodethistWp0808242335 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sponge_detail_history', function (Blueprint $table) {
            $table->string('wp_number', '50')->default('')->after('cr_number');
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
            $table->dropColumn('wp_number');
        });
    }
}
