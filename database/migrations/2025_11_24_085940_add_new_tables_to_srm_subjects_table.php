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
        Schema::table('srm_subjects', function (Blueprint $table) {
            $table->tinyInteger('units')->after('name');
            $table->tinyInteger('clock_hours')->after('units');

            $table->boolean('lab_type')->after('is_lab')->nullable();
            $table->boolean('is_seminar')->default(0)->nullable();
            $table->boolean('has_conflicts')->default(0)->nullable();
            $table->boolean('has_energy')->default(1)->nullable();
            $table->boolean('is_evaluated')->default(1)->nullable();
            $table->boolean('is_graded')->default(0)->nullable();

            $table->boolean('is_major')->default(0)->nullable();
            $table->unsignedBigInteger('program_id')->nullable();

            $table->boolean('is_active')->default(1);

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('program_id')->references('id')->on('sys_departments')->onDelete('set null');

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
        Schema::table('srm_subjects', function (Blueprint $table) {
            
            $table->dropForeign(['program_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);

            $table->dropColumn('units');
            $table->dropColumn('clock_hours');
            $table->dropColumn('lab_type');
            $table->dropColumn('is_seminar');
            $table->dropColumn('has_conflicts');
            $table->dropColumn('has_energy');
            $table->dropColumn('is_evaluated');
            $table->dropColumn('is_graded');
            $table->dropColumn('is_major');
            $table->dropColumn('program_id');
            $table->dropColumn('is_active');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropColumn('deleted_by');
            $table->dropSoftDeletes();
            $table->dropTimestamps();
        });
    }
};
