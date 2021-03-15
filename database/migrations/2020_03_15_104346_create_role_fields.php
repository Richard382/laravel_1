<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function ($table) {
            if (!Schema::hasColumn('roles', 'slug')) {
                $table->string('slug')->unique();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('roles', 'slug')) {
            Schema::table('roles', function ($table) {
                $table->dropColumn('slug');
            });
        }
    }
}
