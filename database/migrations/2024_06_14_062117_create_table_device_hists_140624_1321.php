<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDeviceHists1406241321 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_hists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('device_id');
            $table->string('device_name','150');
            $table->string('device_description','255')->default('');
            $table->unsignedBigInteger('location_id');
            $table->string('department_in_charge','50')->default('');
            $table->string('code','50')->default('');
            $table->string('activa_number','100')->default('');
            $table->boolean('active')->default(1);
            $table->dateTime('start_effective')->nullable();  
            $table->dateTime('end_effective')->nullable();
            $table->string('action','10');
            $table->unsignedBigInteger('created_by'); 
            $table->dateTime('created_at')->nullable();

            $table->index(['device_id', 'device_name'], 'device_hists_1_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_hists');
    }
}
