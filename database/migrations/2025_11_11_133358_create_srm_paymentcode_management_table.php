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
        Schema::create('srm_paymentcode_management', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('paymentcode_id');
            $table->boolean('is_active')->default(1);
            $table->timestamps();

            $table->foreign('paymentcode_id')->references('id')->on('sys_paymentcode_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('srm_paymentcode_management');
    }
};
