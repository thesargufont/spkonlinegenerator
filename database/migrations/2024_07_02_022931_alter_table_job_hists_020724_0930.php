<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableJobHists0207240930 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_hists', function (Blueprint $table) {
            $table->string('job_description','100')->default('')->after('job_category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_hists', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
    }
}
