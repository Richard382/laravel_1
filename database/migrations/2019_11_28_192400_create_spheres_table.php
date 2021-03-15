<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpheresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spheres', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('property_type_id')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->decimal('price_from', 8, 2)->nullable();
            $table->decimal('price_to', 8, 2)->nullable();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('property_type_id')->references('id')->on('property_types');
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
        Schema::dropIfExists('spheres');
    }
}
