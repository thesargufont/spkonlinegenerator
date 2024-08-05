<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIndexGencode0208242144 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_codes', function (Blueprint $table) {
            $table->dropIndex('general_codes_1_index');
            $table->index(['section', 'label', 'reff1', 'reff2', 'reff3', 'reff4', 'reff5', 'value', 'qty', 'default', 'start_effective', 'end_effective'], 'general_codes_1_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_codes', function (Blueprint $table) {
            $table->dropIndex('general_codes_1_index');
            $table->index(['section', 'label', 'reff1'], 'general_codes_1_index');
        });
    }
}
