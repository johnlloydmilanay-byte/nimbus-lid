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
        Schema::create('srm_curriculum_prerequisite', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_subject_id')->nullable();
            $table->unsignedBigInteger('prereq_subject_id')->nullable();

            $table->boolean('is_active')->default(1);

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('curriculum_subject_id')->references('id')->on('srm_curriculum_subject')->onDelete('set null');
            $table->foreign('prereq_subject_id')->references('id')->on('srm_subjects')->onDelete('set null');

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
        Schema::dropIfExists('srm_curriculum_prerequisite');
    }
};
