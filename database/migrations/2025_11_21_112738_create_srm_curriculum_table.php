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
        Schema::create('srm_curriculum', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('year');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('term_id')->nullable();
            $table->unsignedBigInteger('year_level_details_id')->nullable();
            $table->string('course_code');
            $table->string('course_name');
            $table->tinyInteger('lec_units')->default(0)->nullable();
            $table->tinyInteger('lab_units')->default(0)->nullable();
            $table->boolean('is_major')->default(0);

            $table->boolean('is_active')->default(1);

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('department_id')->references('id')->on('sys_departments')->onDelete('set null');
            $table->foreign('term_id')->references('id')->on('sys_terms')->onDelete('set null');
            $table->foreign('year_level_details_id')->references('id')->on('sys_year_levels_details')->onDelete('set null');

            $table->foreign('created_by')->references('user_id')->on('srm_users')->onDelete('set null');
            $table->foreign('updated_by')->references('user_id')->on('srm_users')->onDelete('set null');
            $table->foreign('deleted_by')->references('user_id')->on('srm_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('srm_curriculum');
    }
};
