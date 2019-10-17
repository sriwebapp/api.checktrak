<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('check_id');
            $table->unsignedBigInteger('action_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->string('remarks')->nullable();
            $table->string('state');
            $table->timestamps();

            $table->foreign('check_id')->references('id')->on('checks')->onDelete('cascade');
            $table->foreign('action_id')->references('id')->on('actions');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_history');
    }
}
