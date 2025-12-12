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
        Schema::create('admission_jhs_subtest', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->integer('totalscore');
            $table->integer('priority');
            $table->integer('subtest_group');
            $table->float('weight');
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
        Schema::dropIfExists('admission_jhs_subtest');
    }
};
