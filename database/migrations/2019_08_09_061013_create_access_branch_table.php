<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_branch', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('access_id');
            $table->unsignedBigInteger('branch_id');
            // $table->timestamps();

            $table->foreign('access_id')->references('id')->on('accesses');
            $table->foreign('branch_id')->references('id')->on('branches');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accesses_branch');
    }
}
