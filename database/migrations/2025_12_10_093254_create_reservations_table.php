<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->string('borrower_name');
            $table->string('borrower_type'); // Faculty Member or Student
            $table->text('purpose');
            $table->string('room_no');
            $table->date('date_requested');
            $table->string('term');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('number_of_groups');
            $table->time('time');
            $table->string('program')->nullable();
            $table->string('year_section')->nullable();
            $table->string('subject_code')->nullable();
            $table->string('subject_description')->nullable();
            $table->string('activity_title')->nullable();
            $table->string('activity_no')->nullable();
            
            // Student-specific fields
            $table->string('student_id')->nullable(); // For students only
            $table->string('email')->nullable(); // For students only
            $table->string('contact_number')->nullable(); // For students only
            $table->integer('group_number')->nullable(); // For students: 1, 2, 3, etc.
            
            // Foreign key for student->faculty relationship
            $table->foreignId('faculty_reference_id')->nullable()->constrained('reservations')->onDelete('cascade');
            
            // Status tracking
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected, Cancelled
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}