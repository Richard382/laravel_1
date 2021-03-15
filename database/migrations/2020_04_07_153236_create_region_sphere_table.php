<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegionSphereTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('region_sphere', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sphere_id');
            $table->unsignedBigInteger('region_id');

            $table->foreign('sphere_id')->references('id')->on('spheres')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('regions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('region_sphere');
    }
}
