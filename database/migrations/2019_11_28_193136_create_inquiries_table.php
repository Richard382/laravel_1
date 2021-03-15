<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('service_id');
            $table->longText('requirements');
            $table->decimal('price_from', 8, 2)->default(0);
            $table->decimal('price_to', 8, 2)->default(0);
            $table->boolean('in_hurry')->default(false);
            $table->longText('token')->nullable();
            $table->string('status')->default(\App\Inquiry::STATUS_IN_PROCESS);
            $table->boolean('active')->default(true);
            $table->integer('contact_company')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inquiries');
    }
}
