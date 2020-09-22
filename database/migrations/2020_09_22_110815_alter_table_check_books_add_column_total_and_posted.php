<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCheckBooksAddColumnTotalAndPosted extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('check_books', function (Blueprint $table) {
            $table->unsignedInteger('total')->after('end_series')->default(0);
            $table->unsignedInteger('posted')->after('total')->default(0);
            $table->unsignedInteger('available')->after('total')->default(0);
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
            $table->dropColumn(['total', 'posted', 'available']);
        });
    }
}
