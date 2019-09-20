<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_action', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('access_id');
            $table->unsignedBigInteger('action_id');
            // $table->timestamps();

            $table->foreign('access_id')->references('id')->on('accesses');
            $table->foreign('action_id')->references('id')->on('actions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_action');
    }
}
