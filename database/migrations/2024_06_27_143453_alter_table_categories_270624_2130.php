<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCategories2706242130 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_at'); 
            $table->dateTime('updated_at')->nullable()->after('updated_by');  
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
            $table->dropColumn('updated_by');
            $table->dropColumn('updated_at');
        });
    }
}
