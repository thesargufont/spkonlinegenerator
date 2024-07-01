<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableRoles2406241423 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('roles_1_unique');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->unique(['user_id', 'role', 'authority', 'active', 'start_effective'],'roles_1_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('roles_1_unique');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->unique(['role', 'authority', 'active', 'start_effective'],'roles_1_unique');
        });
    }
}
