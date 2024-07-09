<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSpongeheader0907241144 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sponge_headers', function (Blueprint $table) {
            $table->string('wp_number', '50')->after('spk_number')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sponge_headers', function (Blueprint $table) {
            $table->dropColumn('wp_number');
        });
    }
}
