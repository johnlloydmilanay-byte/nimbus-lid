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
        Schema::create('srm_fees_tuition', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->nullable();
            $table->integer('year_level')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->decimal('rate_regular', 10, 2)->default(0);
            $table->decimal('rate_major', 10, 2)->default(0);
            $table->boolean('setup_type')->default(1);
            $table->string('ar_account');
            $table->string('gl_account');
            $table->boolean('is_active')->default(1);

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('program_id')->references('id')->on('srm_programs')->onDelete('set null');
            $table->foreign('created_by')->references('user_id')->on('srm_users')->onDelete('set null');
            $table->foreign('updated_by')->references('user_id')->on('srm_users')->onDelete('set null');
            $table->foreign('deleted_by')->references('user_id')->on('srm_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('srm_fees_tuition');
    }
};
