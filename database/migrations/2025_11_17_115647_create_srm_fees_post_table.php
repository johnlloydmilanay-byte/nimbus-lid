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
        Schema::create('srm_fees_post', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('term');

            $table->string('fee_name')->nullable();;
            $table->unsignedBigInteger('fee_types_id')->nullable();

            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('deposit', 10, 2)->default(0);

            $table->string('ar_account')->nullable();
            $table->string('gl_account')->nullable();

            $table->unsignedBigInteger('academicgroup_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();

            $table->unsignedBigInteger('class_schedule_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();

            $table->unsignedBigInteger('studentstatus_id')->nullable();
            $table->integer('year_level')->nullable();
            $table->integer('year_entry')->nullable();
            $table->boolean('is_active')->default(1);

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('fee_types_id')->references('id')->on('sys_fees_types')->onDelete('set null');
            $table->foreign('ar_account')->references('accountcode')->on('chartmaster')->onDelete('set null');
            $table->foreign('gl_account')->references('accountcode')->on('chartmaster')->onDelete('set null');
            $table->foreign('academicgroup_id')->references('id')->on('sys_academicgroups')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('sys_departments')->onDelete('set null');
            $table->foreign('class_schedule_id')->references('id')->on('srm_class_schedules')->onDelete('set null');
            $table->foreign('program_id')->references('id')->on('srm_programs')->onDelete('set null');
            $table->foreign('studentstatus_id')->references('id')->on('sys_studentstatus')->onDelete('set null');

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
        Schema::dropIfExists('srm_fees_post');
    }
};
