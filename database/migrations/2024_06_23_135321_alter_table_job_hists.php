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
            if (Schema::hasColumn('job_hists', 'jobs')) {
                $table->dropColumn('jobs');
            }

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

            if (Schema::hasColumn('job_hists', 'departement_id')) {
                $table->dropColumn('departement_id');
            }
            if (Schema::hasColumn('job_hists', 'wo_category')) {
                $table->dropColumn('wo_category');
            }
            if (Schema::hasColumn('job_hists', 'job_category')) {
                $table->dropColumn('job_category');
            }
        });
    }
}
