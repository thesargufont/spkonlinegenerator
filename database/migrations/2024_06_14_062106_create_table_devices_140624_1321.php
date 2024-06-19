<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDevices1406241321 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('device_name','150');
            $table->string('device_description','255')->default('');
            $table->unsignedBigInteger('location_id');
            $table->string('department_in_charge','50')->default('');
            $table->string('code','50')->default('');
            $table->string('activa_number','100')->default('');
            $table->boolean('active')->default(1);
            $table->dateTime('start_effective')->nullable();  
            $table->dateTime('end_effective')->nullable();
            $table->unsignedBigInteger('created_by'); 
            $table->dateTime('created_at')->nullable();  
            $table->unsignedBigInteger('updated_by'); 
            $table->dateTime('updated_at')->nullable();

            $table->unique(['device_name', 'location_id', 'active'],'devices_1_unique');
            $table->index(['device_name', 'active', 'start_effective', 'end_effective'], 'devices_1_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
}
