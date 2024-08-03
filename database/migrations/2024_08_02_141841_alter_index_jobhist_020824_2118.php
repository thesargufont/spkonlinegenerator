<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIndexJobhist0208242118 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_hists', function (Blueprint $table) {
            $table->index(['job_category_id', 'department_id', 'wo_category', 'job_category', 'active', 'start_effective', 'end_effective', 'action'], 'job_hists_1_index');
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
            $table->dropIndex('job_hists_1_index');
        });
    }
}
