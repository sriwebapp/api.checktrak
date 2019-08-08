<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroupActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_group_action', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_group_id');
            $table->unsignedBigInteger('action_id');
            $table->timestamps();

            $table->foreign('user_group_id')->references('id')->on('user_groups');
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
        Schema::dropIfExists('user_group_action');
    }
}
