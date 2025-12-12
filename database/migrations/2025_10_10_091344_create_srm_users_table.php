<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('srm_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id', 50)->unique();
            $table->string('username');
            $table->string('password');
            $table->boolean('usertype');
            $table->boolean('is_active')->default(1)->comment('1 = active, 0 = inactive');
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('srm_users', function (Blueprint $table) {
            $table->foreign('created_by')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('user_id')->on('srm_users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('srm_users');
    }
};
