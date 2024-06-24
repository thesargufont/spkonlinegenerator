<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableJobHists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_hists', function (Blueprint $table) {
            $table->dropColumn('job');

            $table->unsignedBigInteger('departement_id')->nullable()->default(null)->after('id');
            $table->string('wo_category', 100)->default('')->after('departement_id');
            $table->string('job_category', 100)->default('')->after('wo_category');
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
            $table->string('job', 50)->default('')->after('id');

            $table->dropColumn('departement_id');
            $table->dropColumn('wo_category');
            $table->dropColumn('job_category');
        });
    }
}
