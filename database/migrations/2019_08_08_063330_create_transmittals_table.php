<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransmittalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transmittals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('company_id');
            $table->string('year');
            $table->string('series');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('incharge');
            $table->date('date');
            $table->date('due');
            $table->string('ref')->unique();
            $table->date('returned')->nullable();
            $table->boolean('received')->default(0);
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('incharge')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transmittals');
    }
}
