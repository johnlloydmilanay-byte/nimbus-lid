<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admission_shs_score_transmutation', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subtest_id')
                ->constrained('admission_shs_subtests')
                ->onDelete('cascade');

            $table->integer('subtest_group_id')->nullable();
            $table->string('subtest_name')->nullable();

            $table->double('rawscore');
            $table->double('equivalent');

            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('admission_shs_score_transmutation');
    }
};
