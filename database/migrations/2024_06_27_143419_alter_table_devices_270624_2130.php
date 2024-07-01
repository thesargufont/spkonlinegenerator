<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDevices2706242130 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('barand');
            $table->dropColumn('departement_id');

            $table->string('brand','150')->default('')->after('device_description');
            $table->unsignedBigInteger('department_id')->nullable()->default(null)->after('location_id');
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
            $table->dropColumn('brand');
            $table->dropColumn('department_id');

            $table->string('barand','150')->default('')->after('device_description');
            $table->unsignedBigInteger('departement_id')->nullable()->default(null)->after('location_id');
        });
    }
}
