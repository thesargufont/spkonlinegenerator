<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableJobHists2706242130 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('job_hists');

        Schema::create('job_hists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('job_category_id');
            $table->unsignedBigInteger('department_id');
            $table->string('wo_category','100')->default('');
            $table->string('job_category','100')->default('');
            $table->boolean('active')->default(1);
            $table->dateTime('start_effective')->nullable();  
            $table->dateTime('end_effective')->nullable();
            $table->string('action','10');
            $table->unsignedBigInteger('created_by'); 
            $table->dateTime('created_at')->nullable();  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_hists');

        Schema::create('job_hists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('job_id');
            $table->string('job','50');
            $table->string('job_description','100')->default('');
            $table->boolean('active')->default(1);
            $table->dateTime('start_effective')->nullable();  
            $table->dateTime('end_effective')->nullable();
            $table->string('action','10');
            $table->unsignedBigInteger('created_by'); 
            $table->dateTime('created_at')->nullable();  

            $table->index(['job_id', 'job'], 'job_hists_1_index');
        });
    }
}
