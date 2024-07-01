<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBasecampHists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basecamp_hists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('basecamp_id');
            $table->string('basecamp','50');
            $table->string('basecamp_description','100')->default('');
            $table->boolean('active')->default(1);
            $table->dateTime('start_effective')->nullable();  
            $table->dateTime('end_effective')->nullable();
            $table->string('action','10');
            $table->unsignedBigInteger('created_by'); 
            $table->dateTime('created_at')->nullable();  

            $table->index(['basecamp_id', 'basecamp'], 'basecamp_hists_1_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('basecamp_hists');
    }
}
