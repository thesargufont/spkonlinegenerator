<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableJobHistss2406241423 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_hists', function (Blueprint $table) {
            $table->dropColumn('departement_id');
        });
        Schema::table('job_hists', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->default(null)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_hists', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });

        Schema::table('job_hists', function (Blueprint $table) {
            $table->unsignedBigInteger('departement_id')->nullable()->default(null)->after('id');
        });
    }
}
