<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->after('id');
            //$table->string('name', 100)->change();
            $table->string('nik', 10)->after('name');
            //$table->string('email', 100)->change();
            $table->string('gender', 10)->after('email_verified_at');
            $table->boolean('active')->after('gender');
            $table->date('start_effective')->after('active');
            $table->date('end_effective')->after('active')->nullable();
            $table->string('signature_path', 150)->after('end_effective')->nullable();
            $table->unsignedBigInteger('created_by')->after('remember_token');
            $table->unsignedBigInteger('updated_by')->after('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('department_id');
            //$table->string('name')->change();
            $table->dropColumn('nik');
            //$table->string('email')->change();
            $table->dropColumn('gender');
            $table->dropColumn('active');
            $table->dropColumn('start_effective');
            $table->dropColumn('end_effective');
            $table->dropColumn('signature_path');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
}
