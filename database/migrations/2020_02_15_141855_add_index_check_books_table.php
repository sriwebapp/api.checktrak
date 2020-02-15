<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexCheckBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('check_books', function (Blueprint $table) {
            $table->index('start_series');
            $table->index('end_series');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('check_books', function (Blueprint $table) {
            $table->dropIndex('check_books_start_series_index');
            $table->dropIndex('check_books_end_series_index');
        });
    }
}
