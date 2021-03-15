<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('token');
            $table->bigInteger('user_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->float('amount')->unsigned()->nullable();
            $table->integer('payment_status')->unsigned()->default(0);
            $table->string('payment_method');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
