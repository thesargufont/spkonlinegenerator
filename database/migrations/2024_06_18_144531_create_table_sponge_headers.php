<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSpongeHeaders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponge_headers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('wo_number', '50')->default('');
            $table->string('wo_type', '50')->default('');
            $table->string('spk_number', '50')->default('');
            $table->string('job_category', '50')->default('');
            $table->string('department', '50')->default('');
            $table->string('priority', '20')->default('');
            $table->string('description', '150')->default('');
            $table->unsignedBigInteger('approve_by')->nullable();
            $table->dateTime('approve_at')->nullable();
            $table->string('status', '20')->default('');
            $table->dateTime('effective_date')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->dateTime('created_at')->nullable();
            $table->unsignedBigInteger('updated_by');
            $table->dateTime('updated_at')->nullable();

            $table->unique(['wo_number', 'spk_number', 'job_category'], 'table_1_unique');
            $table->index(['department', 'priority'], 'table_1_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sponge_headers');
    }
}
