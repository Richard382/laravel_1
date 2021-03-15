<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('price')->nullable();
            $table->longText('text');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('inquiry_id')->nullable();
            $table->integer('status')->default(\App\Offer::STATUS_DEFAULT);
            $table->boolean('payed')->default(false);
            $table->timestamp('expire_at')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('inquiry_id')->references('id')->on('inquiries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
    }
}
