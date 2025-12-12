<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admission_shs_subtests_result', function (Blueprint $table) {
            $table->id();

            $table->string('application_number_id');
            $table->foreign('application_number_id')
                ->references('application_number')
                ->on('admission_shs')
                ->onDelete('cascade');

            $table->string('subtest_name')->nullable();
            $table->string('subtest_id');

            $table->integer('ts')->nullable();
            $table->integer('rawscore');
            $table->integer('transmutation')->nullable();

            $table->integer('hs_grade')->nullable();
            $table->float('api')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admission_shs_subtests_result');
    }
};
