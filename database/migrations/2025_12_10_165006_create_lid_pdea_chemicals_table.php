<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLidPdeaChemicalsTable extends Migration
{
    public function up()
    {
        Schema::create('lid_pdea_chemicals', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // name of the PDEA item
            $table->boolean('solution')->default(false);
            $table->boolean('concentration')->default(false);
            $table->string('concentration_value')->nullable();
            $table->string('volume')->nullable();
            $table->integer('quantity')->default(0); // number of available containers
            $table->text('instruction')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lid_pdea_chemicals');
    }
}
