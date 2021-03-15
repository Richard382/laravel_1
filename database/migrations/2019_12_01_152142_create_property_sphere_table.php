<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertySphereTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_sphere', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sphere_id');
            $table->unsignedBigInteger('property_id');

            $table->foreign('sphere_id')->references('id')->on('spheres');
            $table->foreign('property_id')->references('id')->on('properties');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_sphere');
    }
}
