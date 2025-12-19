<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLidChemicalsTable extends Migration
{
    public function up()
    {
        Schema::create('lid_chemicals', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('chemical_type')->default('Solid'); // Solid, Liquid, Gas
            $table->boolean('is_solution')->default(false); // Is it a solution?
            $table->string('volume')->nullable(); // e.g., '500ml', '1L'
            $table->string('concentration')->nullable(); // e.g., 0.5M, 10%, etc.
            $table->decimal('quantity', 10, 2)->default(0); // Total available quantity
            $table->string('unit')->default('g'); // g, kg, ml, L, etc.
            $table->decimal('reserved_quantity', 10, 2)->default(0); // Quantity reserved but not yet approved
            $table->text('instruction')->nullable(); // Handling instructions
            $table->text('storage_condition')->nullable(); // Storage requirements
            $table->string('hazard_class')->nullable(); // GHS hazard classification
            $table->string('cas_number')->nullable(); // CAS registry number
            $table->string('supplier')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('location')->nullable(); // Storage location
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Generated column for available quantity (total - reserved)
            $table->decimal('available_quantity', 10, 2)->storedAs('quantity - reserved_quantity');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lid_chemicals');
    }
}