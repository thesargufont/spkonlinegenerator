<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLocations1406241027 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('location','50');
            $table->string('location_description','100')->default('');
            $table->string('location_type','50')->default('');
            $table->string('address','255')->default('');
            $table->string('code','20')->default('');
            $table->string('sub_district','50')->default('');
            $table->string('district','50')->default('');
            $table->string('city','50')->default('');
            $table->string('province','50')->default('');
            $table->string('country','50')->default('');
            $table->boolean('active')->default(1);
            $table->dateTime('start_effective')->nullable();  
            $table->dateTime('end_effective')->nullable();  
            $table->unsignedBigInteger('created_by'); 
            $table->dateTime('created_at')->nullable();  
            $table->unsignedBigInteger('updated_by'); 
            $table->dateTime('updated_at')->nullable();  

            $table->unique(['location','active'],'locations_1_unique');
            $table->index(['location', 'location_type', 'code', 'active', 'start_effective', 'end_effective'], 'locations_1_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
