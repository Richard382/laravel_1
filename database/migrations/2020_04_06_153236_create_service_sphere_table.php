<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceSphereTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_sphere', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sphere_id');
            $table->unsignedBigInteger('service_id');

            $table->foreign('sphere_id')->references('id')->on('spheres')->onDelete('cascade');
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
        Schema::dropIfExists('service_sphere');
    }
}
