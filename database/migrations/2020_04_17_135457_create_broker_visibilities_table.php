<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrokerVisibilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('broker_visibilities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('position')->unique();
            $table->decimal('price_per_month', 8);
            $table->unsignedBigInteger('company_id')->nullable();
            $table->date('taken_until');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('broker_visibilities');
    }
}
