<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDepartments1406241125 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('department','50');
            $table->string('department_description','100')->default('');
            $table->boolean('active')->default(1);
            $table->dateTime('start_effective')->nullable();  
            $table->dateTime('end_effective')->nullable();
            $table->unsignedBigInteger('created_by'); 
            $table->dateTime('created_at')->nullable();  
            $table->unsignedBigInteger('updated_by'); 
            $table->dateTime('updated_at')->nullable();  

            $table->unique(['department','active'],'departments_1_unique');
            $table->index(['department', 'active', 'start_effective', 'end_effective'], 'departments_1_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
}
