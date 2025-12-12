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
        Schema::create('admission_pse_subtest_g1_rating', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subtest_id');
            $table->foreign('subtest_id')->references('id')->on('admission_pse_elem_subtest')->onDelete('cascade');
            $table->integer('score');
            $table->string('rating');
            $table->boolean('is_active')->default(1)->comment('1 = active, 0 = inactive');
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
        Schema::dropIfExists('admission_pse_subtest_g1_rating');
    }
};
