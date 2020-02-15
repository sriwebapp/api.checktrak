<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexPayeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payees', function (Blueprint $table) {
            $table->index('code');
            $table->index('name');
            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payees', function (Blueprint $table) {
            $table->dropIndex('payees_code_index');
            $table->dropIndex('payees_name_index');
            $table->dropIndex('payees_active_index');
        });
    }
}
