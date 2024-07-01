<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDeviceCategories2406241423 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_categories', function (Blueprint $table) {
            $table->dropColumn('action');

            $table->unique(['device_category','disturbance_category', 'active'],'device_categories_1_unique');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_categories', function (Blueprint $table) {
            $table->string('action','10')->default('')->after('end_effective'); 

            $table->dropUnique('device_categories_1_unique');
        });
    }
}
