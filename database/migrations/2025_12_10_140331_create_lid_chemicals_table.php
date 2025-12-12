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
            $table->string('name')->unique(); // name of the chemical
            $table->boolean('solution')->default(false);
            $table->boolean('concentration')->default(false);
            $table->string('concentration_value')->nullable(); // e.g., 0.5M
            $table->string('volume')->nullable(); // e.g., 500ml
            $table->integer('quantity')->default(0); // number of available containers
            $table->text('instruction')->nullable(); // handling instructions
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lid_chemicals');
    }
}
