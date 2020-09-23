<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_report', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('access_id');
            $table->unsignedBigInteger('report_id');
            // $table->timestamps();

            $table->foreign('access_id')->references('id')->on('accesses');
            $table->foreign('report_id')->references('id')->on('reports');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_report');
    }
}
