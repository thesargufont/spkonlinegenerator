<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBasecamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basecamps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('basecamp','50');
            $table->string('basecamp_description','100')->default('');
            $table->boolean('active')->default(1);
            $table->dateTime('start_effective')->nullable();  
            $table->dateTime('end_effective')->nullable();
            $table->unsignedBigInteger('created_by'); 
            $table->dateTime('created_at')->nullable();  
            $table->unsignedBigInteger('updated_by'); 
            $table->dateTime('updated_at')->nullable();  

            $table->unique(['basecamp','active'],'basecamps_1_unique');
            $table->index(['basecamp', 'active', 'start_effective', 'end_effective'], 'basecamps_1_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('basecamps');
    }
}
