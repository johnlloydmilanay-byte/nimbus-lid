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
        Schema::table('reservations', function (Blueprint $table) {
            // Add return tracking fields
            $table->boolean('glassware_returned')->default(false)->after('contact_number');
            $table->date('glassware_return_date')->nullable()->after('glassware_returned');
            $table->text('glassware_return_notes')->nullable()->after('glassware_return_date');
            
            $table->boolean('equipment_returned')->default(false)->after('glassware_return_notes');
            $table->date('equipment_return_date')->nullable()->after('equipment_returned');
            $table->text('equipment_return_notes')->nullable()->after('equipment_return_date');
            
            // Add LID personnel notes field with SRM user tracking
            $table->text('lid_notes')->nullable()->after('equipment_return_notes');
            $table->foreignId('updated_by')->nullable()->after('lid_notes');
            
            // Add indexes
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'glassware_returned',
                'glassware_return_date',
                'glassware_return_notes',
                'equipment_returned',
                'equipment_return_date',
                'equipment_return_notes',
                'lid_notes',
                'updated_by'
            ]);
        });
    }
};