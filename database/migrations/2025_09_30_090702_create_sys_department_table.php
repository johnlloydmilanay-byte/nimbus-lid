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
    public function up()
    {
        Schema::create('sys_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academicgroup_id')
                ->nullable()
                ->constrained('sys_academicgroups')
                ->onDelete('cascade');
            $table->string('code');
            $table->string('name');
            $table->boolean('is_active')->default(1)->comment('1 = active, 0 = inactive');
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
        Schema::dropIfExists('sys_departments');
    }
};
