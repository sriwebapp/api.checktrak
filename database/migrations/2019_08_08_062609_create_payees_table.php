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
            $table->string('code', 20);
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('payee_group_id')->nullable();
            $table->boolean('active')->default(1);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('payee_group_id')->references('id')->on('payee_groups');

            $table->unique(['code', 'company_id']);
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
