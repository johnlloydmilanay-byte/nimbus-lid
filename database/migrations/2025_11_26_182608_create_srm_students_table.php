<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('srm_students', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('application_number')->unique();
            $table->string('student_number')->nullable();
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename')->nullable();

            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('studentstatus_id')->nullable();
            $table->unsignedBigInteger('year_level_id')->nullable();

            $table->string('year_entry')->nullable();
            $table->string('gender')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('email')->nullable();
            $table->integer('no_of_siblings')->nullable();
            $table->date('dob')->nullable();
            $table->string('birthplace')->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->nullable();

            // Permanent Address
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('barangay')->nullable();
            $table->string('staying_in')->nullable();

            // Current Address
            $table->string('current_province_id')->nullable();
            $table->string('current_city_id')->nullable();
            $table->string('current_barangay')->nullable();

            // Educational Background
            $table->string('elem_school_name')->nullable();
            $table->string('elem_address')->nullable();
            $table->string('elem_school_year_attended')->nullable();
            $table->string('jhs_name')->nullable();
            $table->string('jhs_address')->nullable();
            $table->string('jhs_year_attended')->nullable();
            $table->text('awards')->nullable();
            $table->string('organization')->nullable();
            $table->string('position')->nullable();

            // Family Background - Father
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->integer('father_age')->nullable();
            $table->string('father_education')->nullable();
            $table->string('father_mobile_no')->nullable();
            $table->string('father_status')->nullable();
            $table->string('father_placework')->nullable();
            $table->string('father_ofw_status')->nullable();

            // Family Background - Mother
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->integer('mother_age')->nullable();
            $table->string('mother_education')->nullable();
            $table->string('mother_mobile_no')->nullable();
            $table->string('mother_status')->nullable();
            $table->string('mother_placework')->nullable();
            $table->string('mother_ofw_status')->nullable();

            // Family Background - Guardian
            $table->string('guardian_name')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->string('guardian_number')->nullable();

            // Parents' Status & Income
            $table->string('parents_marital_status')->nullable();
            $table->string('monthly_family_income')->nullable();
            $table->string('family_living_arrangement')->nullable();
            $table->string('others_specify')->nullable();

            // Other Information
            $table->boolean('is_pwd')->default(false);
            $table->string('is_pwd_yes')->nullable();
            $table->boolean('is_scholar')->default(false);
            $table->string('is_scholar_type')->nullable();
            $table->string('is_scholar_yes_others')->nullable();

            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();

            $table->string('deleted_by', 50)->nullable();
            $table->softDeletes();

            $table->foreign('department_id')->references('id')->on('sys_departments')->onDelete('set null');
            $table->foreign('program_id')->references('id')->on('srm_programs')->onDelete('set null');
            $table->foreign('studentstatus_id')->references('id')->on('sys_studentstatus')->onDelete('set null');
            $table->foreign('year_level_id')->references('id')->on('sys_year_levels_details')->onDelete('set null');

            $table->foreign('province_id')->references('id')->on('sys_address_provinces')->onDelete('set null');
            $table->foreign('city_id')->references('id')->on('sys_address_towns')->onDelete('set null');

            $table->foreign('created_by')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('user_id')->on('srm_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('srm_students');
    }
};
