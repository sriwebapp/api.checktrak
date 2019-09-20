<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessModuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_module', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('access_id');
            $table->unsignedBigInteger('module_id');
            // $table->timestamps();

            $table->foreign('access_id')->references('id')->on('accesses');
            $table->foreign('module_id')->references('id')->on('modules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_module');
    }
}
