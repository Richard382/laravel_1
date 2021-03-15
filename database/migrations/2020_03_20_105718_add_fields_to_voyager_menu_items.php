<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToVoyagerMenuItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_items', function ($table) {
            if (!Schema::hasColumn('menu_items', 'button')) {
                $table->string('button')->nullable();
            }

            if (!Schema::hasColumn('menu_items', 'class')) {
                $table->string('class')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('menu_items', 'button')) {
            Schema::table('menu_items', function ($table) {
                $table->dropColumn('button');
            });
        }

        if (Schema::hasColumn('menu_items', 'class')) {
            Schema::table('menu_items', function ($table) {
                $table->dropColumn('class');
            });
        }
    }
}
