<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('number', 15);
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('payee_id');
            $table->unsignedBigInteger('group_id')->default(1); /*head office*/
            $table->unsignedBigInteger('branch_id')->default(1); /*disbursement*/
            $table->unsignedBigInteger('import_id')->nullable(); /*disbursement*/
            $table->boolean('received')->default(1);
            $table->decimal('amount', 20, 2);
            $table->decimal('cleared', 20, 2)->nullable();
            $table->string('details')->nullable();
            $table->date('date');
            $table->softDeletes();
            $table->timestamps();
            // $table->unique(['number', 'account_id']); unneccessary
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->foreign('branch_id')->references('id')->on('branches');
            // $table->foreign('payee_id')->references('id')->on('payees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checks');
    }
}
