<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSpongeDetail0407240025 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sponge_details', function (Blueprint $table) {
            $table->dateTime('start_at')->after('job_attachment3')->nullable();
            $table->dateTime('estimated_end')->after('start_at')->nullable();
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
            $table->dropColumn('start_at');
            $table->dropColumn('estimated_end');
        });
    }
}
