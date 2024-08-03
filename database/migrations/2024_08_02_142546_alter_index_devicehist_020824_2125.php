<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIndexDevicehist0208242125 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_hists', function (Blueprint $table) {
            $table->dropIndex('device_hists_1_index');
            $table->index(['device_id', 'device_name', 'brand', 'location_id', 'department_id', 'device_category_id', 'serial_number', 'activa_number', 'active', 'start_effective', 'end_effective', 'action'], 'device_hists_1_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_hists', function (Blueprint $table) {
            $table->dropIndex('device_hists_1_index');
            $table->index(['device_id', 'device_name'], 'device_hists_1_index');
        });
    }
}
