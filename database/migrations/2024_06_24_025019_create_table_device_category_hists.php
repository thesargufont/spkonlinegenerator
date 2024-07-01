<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDeviceCategoryHists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_category_hists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('device_category_id');
            $table->string('device_category','150');
            $table->string('disturbance_category','150')->default('');
            $table->boolean('active')->default(1);
            $table->dateTime('start_effective')->nullable();  
            $table->dateTime('end_effective')->nullable();
            $table->string('action','10');
            $table->unsignedBigInteger('created_by'); 
            $table->dateTime('created_at')->nullable();  

            $table->index(['device_category_id', 'device_category', 'disturbance_category', 'active'], 'device_hists_1_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_category_hists');
    }
}
