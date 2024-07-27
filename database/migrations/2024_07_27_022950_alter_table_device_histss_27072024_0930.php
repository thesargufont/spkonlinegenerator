<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDeviceHistss270720240930 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_hists', function (Blueprint $table) {
            $table->renameColumn('eq_id', 'activa_number');
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
            $table->renameColumn('activa_number', 'eq_id');
        });
    }
}
