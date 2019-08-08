<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('desc');
            $table->unsignedBigInteger('payee_group_id')->nullable();
            $table->timestamps();

            $table->foreign('payee_group_id')->references('id')->on('payee_groups');
        });

        Schema::table('checks', function (Blueprint $table) {
            $table->foreign('payee_id')->references('id')->on('payees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payees');
    }
}
