<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIndexDevcathist0208242139 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_category_hists', function (Blueprint $table) {
            $table->dropIndex('device_hists_1_index');
            $table->index(['device_category_id', 'device_category', 'disturbance_category', 'active', 'start_effective', 'end_effective'], 'device_hists_1_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_category_hists', function (Blueprint $table) {
            $table->dropIndex('device_hists_1_index');
            $table->index(['device_category_id', 'device_category', 'disturbance_category', 'active'], 'device_hists_1_index');
        });
    }
}
