<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIndexDephist0208242112 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('department_hists', function (Blueprint $table) {
            $table->dropIndex('department_hists_1_index');
            $table->index(['department_id', 'department', 'department_code', 'active', 'start_effective', 'end_effective', 'action'], 'department_hists_1_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('department_hists', function (Blueprint $table) {
            $table->dropIndex('department_hists_1_index');
            $table->index(['department_id', 'department'], 'department_hists_1_index');
        });
    }
}
