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
        Schema::create('srm_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('year');
            $table->unsignedBigInteger('term_id')->nullable();
            $table->string('or_number')->nullable()->unique();
            $table->string('application_number');
            $table->string('student_id')->nullable();
            $table->string('payor_name')->nullable();
            $table->unsignedBigInteger('payment_code_type_id')->nullable();
            $table->unsignedBigInteger('payment_for_id')->nullable();
            $table->decimal('amount_due', 10, 2)->nullable();
            $table->decimal('amount_to_pay', 10, 2);
            $table->decimal('amount_tendered', 10, 2);
            $table->decimal('change', 10, 2);
            $table->unsignedBigInteger('payment_type_id')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('payment_date');
            $table->string('cashier_id')->nullable();

            $table->boolean('is_active')->default(1);

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('term_id')->references('id')->on('sys_terms')->onDelete('set null');

            $table->foreign('payment_code_type_id')->references('id')->on('sys_paymentcode_types')->onDelete('set null');
            $table->foreign('payment_for_id')->references('id')->on('srm_paymentcode_management')->onDelete('set null');
            $table->foreign('payment_type_id')->references('id')->on('sys_payment_types')->onDelete('set null');

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
        Schema::dropIfExists('srm_payments');
    }
};
