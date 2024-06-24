<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableJobs2406241423 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropUnique('jobs_1_unique');
            $table->dropColumn('departement_id');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->default(null)->after('id');
            $table->unique(['department_id','wo_category','job_category', 'active'],'jobs_1_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropUnique('jobs_1_unique');
            $table->dropColumn('department_id');
        });
        Schema::table('jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('departement_id')->nullable()->default(null)->after('id');
            $table->unique(['departement_id','wo_category','job_category', 'active'],'jobs_1_unique');
        });
    }
}
