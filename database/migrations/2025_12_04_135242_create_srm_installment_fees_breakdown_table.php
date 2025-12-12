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
        Schema::create('srm_installment_fees_breakdown', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('installment_scheme_id')->nullable();
            $table->BigInteger('payment_count')->nullable();
            $table->unsignedBigInteger('fee_management_id')->nullable(); //srm_fees_management
            $table->unsignedBigInteger('fee_post_id')->nullable(); //srm_fees_post
            // $table->decimal('rate', 10, 2);
            $table->BigInteger('rate');

            $table->boolean('is_active')->default(1);

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('installment_scheme_id')->references('id')->on('srm_installment_scheme')->onDelete('set null');
            $table->foreign('fee_management_id')->references('id')->on('srm_fees_management')->onDelete('set null');
            $table->foreign('fee_post_id')->references('id')->on('srm_fees_post')->onDelete('set null');

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
        Schema::dropIfExists('srm_installment_fees_breakdown');
    }
};
