<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSpongeDetailHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponge_detail_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('wo_number_id');
            $table->string('cr_number', '50')->default('');
            $table->string('reporter_location', '50')->default('');
            $table->unsignedBigInteger('device_id');
            $table->string('disturbance_category', '50')->default('');
            $table->string('wo_description', '255')->default('');
            $table->string('job_description', '255')->default('');
            $table->unsignedBigInteger('job_executor')->nullable();
            $table->unsignedBigInteger('job_supervisor')->nullable();
            $table->unsignedBigInteger('job_aid')->nullable();
            $table->string('executor_progress', '50')->default('');
            $table->string('executor_desc', '255')->default('');
            $table->string('wo_attachment1', '150')->default('');
            $table->string('wo_attachment2', '150')->default('');
            $table->string('wo_attachment3', '150')->default('');
            $table->string('job_attachment1', '150')->default('');
            $table->string('job_attachment2', '150')->default('');
            $table->string('job_attachment3', '150')->default('');
            $table->dateTime('start_at');
            $table->dateTime('estimated_end');
            $table->dateTime('close_at')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->string('action', '20')->default('');
            $table->unsignedBigInteger('created_by');
            $table->dateTime('created_at')->nullable();
            $table->unsignedBigInteger('updated_by');
            $table->dateTime('updated_at')->nullable();

            $table->unique(['reporter_location', 'device_id'], 'table_1_unique');
            $table->index(['disturbance_category', 'job_executor', 'job_supervisor', 'job_aid'], 'table_1_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sponge_detail_history');
    }
}
