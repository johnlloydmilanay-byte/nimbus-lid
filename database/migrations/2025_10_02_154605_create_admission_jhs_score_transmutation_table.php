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
        Schema::create('admission_jhs_score_transmutation', function (Blueprint $table) {
            $table->id();
            $table->integer('group_id');
            $table->foreignId('subtest_id')->constrained('admission_jhs_subtest')->onDelete('cascade');
            $table->string('subtest_name')->nullable();
            $table->double('rawscore');
            $table->double('equivalent');
            $table->string('diq')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('admission_jhs_score_transmutation');
    }
};
