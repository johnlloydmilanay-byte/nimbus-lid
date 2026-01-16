<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            
            // Request Information
            $table->date('date_reported');
            $table->enum('request_type', [
                'Masonry',
                'Roofing', 
                'Electrical',
                'Aluminum and Glass Concerns',
                'Carpentry',
                'Plumbing'
            ]);
            
            // Specific reports based on request type
            $table->string('specific_report');
            $table->text('location');
            $table->text('remarks')->nullable();
            
            // Status tracking
            $table->enum('status', ['Pending', 'In Progress', 'Completed', 'Cancelled'])->default('Pending');
            
            // Personnel involved
            $table->string('reported_by');
            $table->string('received_by')->nullable();
            $table->string('endorsed_to')->nullable();
            $table->string('attested_by')->nullable();
            
            // Dates
            $table->date('date_completed')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};