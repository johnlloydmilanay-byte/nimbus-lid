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
        Schema::create('admission_college_subtests_result', function (Blueprint $table) {
            $table->id();

            $table->string('application_number_id');
            $table->foreign('application_number_id')
                ->references('application_number')
                ->on('admission_college')
                ->onDelete('cascade');

            $table->string('name');
            $table->string('subtest');
            $table->string('subtest_id');

            $table->integer('ts');
            $table->double('rawscore');
            $table->double('transmutation');

            $table->double('hs_grade');
            $table->float('api');

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
        Schema::dropIfExists('admission_college_subtests_result');
    }
};
