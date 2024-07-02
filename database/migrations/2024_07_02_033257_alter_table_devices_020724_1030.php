<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDevices0207241030 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropUnique('devices_1_unique');
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->unique(['device_name', 'brand', 'location_id', 'department_id', 'device_category_id', 'serial_number', 'eq_id', 'active'],'devices_1_unique');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropUnique('devices_1_unique');
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->unique(['device_name', 'location_id', 'active'],'devices_1_unique');
        });
    }
}
