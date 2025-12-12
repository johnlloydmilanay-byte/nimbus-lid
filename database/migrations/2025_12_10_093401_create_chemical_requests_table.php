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
            $table->string('name');
            $table->boolean('solution')->default(false);
            $table->boolean('concentration')->default(false);
            $table->string('concentration_value')->nullable();
            $table->string('volume')->nullable();
            $table->text('instruction')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chemical_requests');
    }
}