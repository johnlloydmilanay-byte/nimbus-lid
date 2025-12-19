<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChemicalRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('chemical_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Chemical name
            $table->boolean('is_solution')->default(false);
            $table->string('concentration')->nullable();
            $table->decimal('quantity_per_group', 10, 2); // Quantity needed per student group
            $table->decimal('total_quantity', 10, 2); // Total quantity needed (quantity_per_group × number_of_groups)
            $table->string('unit'); // Unit of measurement
            $table->text('instruction')->nullable(); // Special instructions
            $table->timestamps();
        });
        
        Schema::create('pdea_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Chemical name
            $table->boolean('is_solution')->default(false);
            $table->string('concentration')->nullable();
            $table->decimal('quantity_per_group', 10, 2); // Quantity needed per student group
            $table->decimal('total_quantity', 10, 2); // Total quantity needed (quantity_per_group × number_of_groups)
            $table->string('unit'); // Unit of measurement
            $table->text('instruction')->nullable(); // Special instructions
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pdea_requests');
        Schema::dropIfExists('chemical_requests');
    }
}