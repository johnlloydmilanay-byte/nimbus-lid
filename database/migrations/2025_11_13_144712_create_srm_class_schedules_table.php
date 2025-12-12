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
        Schema::create('srm_class_schedules', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('term');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('section')->nullable();

            $table->foreign('subject_id')->references('id')->on('srm_subjects')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('srm_class_schedules');
    }
};
