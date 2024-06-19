<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDeviceGeneralCodes1406241321 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('section','50');
            $table->string('label','150')->default('');
            $table->string('reff1','150')->default('');
            $table->string('reff2','150')->default('');
            $table->string('reff3','150')->default('');
            $table->string('reff4','150')->default('');
            $table->string('reff5','150')->default('');
            $table->decimal('value', 19, 7)->default(0);
            $table->integer('qty')->default(0);
            $table->boolean('default')->default(0);
            $table->dateTime('start_effective')->nullable();  
            $table->dateTime('end_effective')->nullable();
            $table->unsignedBigInteger('created_by'); 
            $table->dateTime('created_at')->nullable();  
            $table->unsignedBigInteger('updated_by'); 
            $table->dateTime('updated_at')->nullable();

            $table->index(['section', 'label', 'reff1'], 'general_codes_1_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_codes');
    }
}
