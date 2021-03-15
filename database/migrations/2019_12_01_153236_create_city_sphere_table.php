<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitySphereTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_sphere', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sphere_id');
            $table->unsignedBigInteger('city_id');

            $table->foreign('sphere_id')->references('id')->on('spheres');
            $table->foreign('city_id')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_sphere');
    }
}
