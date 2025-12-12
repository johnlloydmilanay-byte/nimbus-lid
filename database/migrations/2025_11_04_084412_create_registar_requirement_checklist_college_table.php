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
        Schema::create('registar_requirement_checklist_college', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->nullable()->index();

            // CAT
            $table->boolean('has_college_result')->default(0)->comment('1 = true, 0 = false');
            $table->string('college_result_signed_by')->nullable();
            $table->timestamp('college_result_signed_at')->nullable();
            $table->string('college_result_updated_by')->nullable();
            $table->timestamp('college_result_updated_at')->nullable();

            // SHS Report Card
            $table->boolean('has_college_report_card')->default(0)->comment('1 = true, 0 = false');
            $table->string('college_report_card_signed_by')->nullable();
            $table->timestamp('college_report_card_signed_at')->nullable();
            $table->string('college_report_card_updated_by')->nullable();
            $table->timestamp('college_report_card_updated_at')->nullable();

            // Certificate of Good Moral
            $table->boolean('has_college_good_moral')->default(0)->comment('1 = true, 0 = false');
            $table->string('college_good_moral_signed_by')->nullable();
            $table->timestamp('college_good_moral_signed_at')->nullable();
            $table->string('college_good_moral_updated_by')->nullable();
            $table->timestamp('college_good_moral_updated_at')->nullable();

            // PSA
            $table->boolean('has_college_psa')->default(0)->comment('1 = true, 0 = false');
            $table->string('college_psa_signed_by')->nullable();
            $table->timestamp('college_psa_signed_at')->nullable();
            $table->string('college_psa_updated_by')->nullable();
            $table->timestamp('college_psa_updated_at')->nullable();

            // 2x2
            $table->boolean('has_college_pic')->default(0)->comment('1 = true, 0 = false');
            $table->string('college_pic_signed_by')->nullable();
            $table->timestamp('college_pic_signed_at')->nullable();
            $table->string('college_pic_updated_by')->nullable();
            $table->timestamp('college_pic_updated_at')->nullable();

            // Brown Envelope
            $table->boolean('has_college_envelope')->default(0)->comment('1 = true, 0 = false');
            $table->string('college_envelope_signed_by')->nullable();
            $table->timestamp('college_envelope_signed_at')->nullable();
            $table->string('college_envelope_updated_by')->nullable();
            $table->timestamp('college_envelope_updated_at')->nullable();

            // timestamps
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('college_result_signed_by', 'fk_college_cat_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('college_report_card_signed_by', 'fk_college_shs_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('college_good_moral_signed_by', 'fk_college_goodmoral_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('college_psa_signed_by', 'fk_college_psa_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('college_pic_signed_by', 'fk_college_2x2_user')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('college_envelope_signed_by', 'fk_college_envelope_user')->references('user_id')->on('srm_users')->onDelete('cascade');

            $table->foreign('college_result_updated_by', 'fk_college_cat_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('college_report_card_updated_by', 'fk_college_shs_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('college_good_moral_updated_by', 'fk_college_goodmoral_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('college_psa_updated_by', 'fk_college_psa_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('college_pic_updated_by', 'fk_college_2x2_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('college_envelope_updated_by', 'fk_college_envelope_user_updated')->references('user_id')->on('srm_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registar_requirement_checklist_college');
    }
};
