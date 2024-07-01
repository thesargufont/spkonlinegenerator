<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDeviceHists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_hists', function (Blueprint $table) {
            $table->dropColumn('department_in_charge');
            $table->dropColumn('code');
            $table->dropColumn('activa_number');

            $table->string('barand','150')->default('')->after('device_description');
            $table->unsignedBigInteger('departement_id')->nullable()->default(null)->after('location_id');
            $table->unsignedBigInteger('device_category_id')->nullable()->default(null)->after('departement_id');
            $table->string('serial_number','100')->default('')->after('device_category_id');
            $table->string('eq_id','50')->default('')->after('serial_number');
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
            $table->string('department_in_charge','50')->default('')->after('location_id');
            $table->string('code','50')->default('')->after('department_in_charge');
            $table->string('activa_number','100')->default('')->after('code');

            $table->dropColumn('barand');
            $table->dropColumn('departement_id');
            $table->dropColumn('device_category_id');
            $table->dropColumn('serial_number');
            $table->dropColumn('eq_id');
        });
    }
}
