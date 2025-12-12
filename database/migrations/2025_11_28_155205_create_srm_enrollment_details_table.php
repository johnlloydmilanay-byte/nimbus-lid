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
        Schema::create('srm_enrollment_details', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->nullable();
            $table->unsignedBigInteger('term_id')->nullable();
            $table->string('application_number')->nullable();
            $table->string('student_number')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->boolean('is_changeofreg')->default(0);
            $table->boolean('is_dropped')->default(0);
            $table->boolean('registrar_drop')->default(0);
            $table->boolean('registrar_warning')->default(0);
            $table->boolean('is_active')->default(1);

            $table->string('prelim', 20)->nullable();
            $table->string('midterm', 20)->nullable();
            $table->string('prefinal', 20)->nullable();
            $table->string('final', 20)->nullable();

            $table->string('dean_by', 50)->nullable();
            $table->timestamp('prelimdean_at')->nullable();
            $table->timestamp('midtermdean_at')->nullable();
            $table->timestamp('prefinaldean_at')->nullable();
            $table->timestamp('finaldean_at')->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('term_id')->references('id')->on('sys_terms')->onDelete('set null');
            $table->foreign('subject_id')->references('id')->on('srm_subjects')->onDelete('set null');
            
            $table->foreign('dean_by')->references('user_id')->on('srm_users')->onDelete('set null');

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
        Schema::dropIfExists('srm_enrollment_details');
    }
};
