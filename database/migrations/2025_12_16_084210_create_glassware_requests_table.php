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
        Schema::create('glassware_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->string('name');
            $table->string('type');
            $table->decimal('quantity_per_group', 10, 2);
            $table->decimal('total_quantity', 10, 2);
            $table->string('unit');
            $table->text('instruction')->nullable();
            $table->timestamps();
            
            $table->index('reservation_id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('glassware_requests');
    }
};