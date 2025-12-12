<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_year_levels_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('year_level_id')->nullable();
            $table->tinyInteger('code');
            $table->string('name');
            $table->tinyInteger('order');
            
            $table->foreign('year_level_id')->references('id')->on('sys_year_levels')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_year_levels_details');
    }
};
