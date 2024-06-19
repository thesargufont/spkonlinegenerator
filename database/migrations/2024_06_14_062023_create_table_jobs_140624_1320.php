<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableJobs1406241320 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('job','50');
            $table->string('job_description','100')->default('');
            $table->boolean('active')->default(1);
            $table->dateTime('start_effective')->nullable();  
            $table->dateTime('end_effective')->nullable();
            $table->unsignedBigInteger('created_by'); 
            $table->dateTime('created_at')->nullable();  
            $table->unsignedBigInteger('updated_by'); 
            $table->dateTime('updated_at')->nullable();  

            $table->unique(['job','active'],'jobs_1_unique');
            $table->index(['job', 'active', 'start_effective', 'end_effective'], 'jobs_1_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
