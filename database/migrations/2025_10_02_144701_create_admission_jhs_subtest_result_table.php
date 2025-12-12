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
        Schema::create('admission_jhs_subtest_result', function (Blueprint $table) {
            $table->id();
            $table->string('application_number_id');
            $table->foreign('application_number_id')->references('application_number')->on('admission_jhs')->onDelete('cascade');
            $table->string('subtest_name')->nullable();
            $table->string('subtest_id');
            $table->integer('ts')->nullable();
            $table->float('rawscore');
            $table->integer('transmutation')->nullable();
            $table->float('hs_grade')->nullable();
            $table->float('api')->nullable();
            $table->float('percentage')->nullable();
            $table->float('equivalent')->nullable();
            $table->string('diq', 20)->nullable();
            $table->string('description', 50)->nullable();
            $table->float('rating')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admission_jhs_subtest_result');
    }
};
