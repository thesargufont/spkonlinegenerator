<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSpongeHeaders270720240954 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sponge_headers', function (Blueprint $table) {
            $table->renameColumn('wo_type', 'wo_category');
            $table->unsignedBigInteger('department_id')->after('job_category');
            $table->dropColumn('department');

            $table->dropUnique('table_1_unique');
            $table->unique(['wo_number', 'wo_category', 'spk_number', 'job_category', 'department_id'],'sponge_header_1_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sponge_headers', function (Blueprint $table) {
            $table->renameColumn('wo_category', 'wo_type');
            $table->string('department', '50')->after('job_category')->default('');
            $table->dropColumn('department_id');

            $table->dropUnique('sponge_header_1_unique');
            $table->unique(['wo_number', 'spk_number', 'job_category'],'table_1_unique');
        });
    }
}
