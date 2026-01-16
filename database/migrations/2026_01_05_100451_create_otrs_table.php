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
        Schema::create('otrs', function (Blueprint $table) {
            $table->id();
            
            // Student Information
            $table->string('Last_Name');
            $table->string('First_Name');
            $table->string('Middle_Name')->nullable();
            $table->string('Student_ID')->unique(); // Renamed from Alumni_ID_Number
            $table->unsignedBigInteger('Degree_Course')->nullable(); // FK to srm_programs
            
            // Static / Readonly Fields
            $table->text('Exemption_Note')->nullable();
            $table->string('Accreditation_Level')->nullable();
            $table->string('CHED_Memo_Order')->nullable();
            
            // Dates & Files
            $table->date('Date_of_Graduation')->nullable();
            $table->string('NSTP_Serial_Number')->nullable();
            $table->string('Photo_Path')->default('assets/photos/default.jpg');
            
            // Admission / Entrance Data
            $table->string('Admission_Credentials')->nullable();
            $table->string('Category')->nullable();
            $table->string('School_Last_Attended')->nullable();
            $table->string('School_Year_Last_Attended')->nullable();
            $table->string('School_Address')->nullable();
            $table->string('Semester_Year_Admitted')->nullable();
            $table->string('College')->nullable();
            
            // Personal Information
            $table->text('Address')->nullable();
            $table->date('Birth_Date')->nullable();
            $table->string('Birth_Place')->nullable();
            $table->string('Citizenship')->nullable();
            $table->string('Religion')->nullable();
            $table->string('Gender')->nullable();
            
            // Document Processing Info
            $table->string('Prepared_By')->nullable();
            $table->string('Checked_By')->nullable();
            $table->string('Dean_Name')->nullable();
            $table->string('Registrar_Name')->nullable();
            $table->date('Date_Prepared')->nullable();

            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('Degree_Course')->references('id')->on('srm_programs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otrs');
    }
};