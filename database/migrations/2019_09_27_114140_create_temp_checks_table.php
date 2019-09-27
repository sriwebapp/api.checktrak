<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_checks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('import_id');
            $table->string('bank');
            $table->string('account');
            $table->string('number');
            $table->string('payee_name');
            $table->string('payee_code');
            $table->string('amount');
            $table->string('details');
            $table->string('date');
            $table->unsignedBigInteger('reason_id');
            $table->timestamps();

            $table->foreign('import_id')->references('id')->on('imports')->onDelete('cascade');
            $table->foreign('reason_id')->references('id')->on('failure_reasons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_checks');
    }
}
