<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckTransmittalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_transmittal', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('check_id');
            $table->unsignedBigInteger('transmittal_id');
            $table->timestamps();

            $table->foreign('check_id')->references('id')->on('checks');
            $table->foreign('transmittal_id')->references('id')->on('transmittals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_transmittal');
    }
}
