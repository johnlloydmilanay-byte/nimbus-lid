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
    public function up(): void
    {
        Schema::create('otr_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('otr_id')->constrained('otrs')->onDelete('cascade');
            
            // Grade Information
            $table->string('school_year'); // e.g., "2023-2024"
            $table->string('semester'); // e.g., "First", "Second", "Summer"
            $table->string('subject_code');
            $table->string('subject_title');
            $table->string('type')->default('Lecture'); // Lecture, Lab, Lecture/Lab
            $table->decimal('final_rating', 5, 2); // e.g., 1.25
            $table->decimal('units_earned', 4, 2); // e.g., 3.00
            
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
        Schema::dropIfExists('otr_grades');
    }
};
