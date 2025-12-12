<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionShsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admission_shs', function (Blueprint $table) {
            $table->id();

            // application number
            $table->string('application_number')->unique();

            // school year / term
            $table->string('year');
            $table->string('term');

            // student information
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('suffix')->nullable();
            $table->string('gender');
            $table->string('mobile_no');
            $table->string('email');
            $table->date('dob');
            $table->integer('age');
            $table->string('nationality');
            $table->string('religion');
            $table->string('zip_code');
            $table->text('address');
            $table->string('contact_person');
            $table->string('contact_number');

            // last school attended
            $table->string('school_name');
            $table->string('lrn');
            $table->text('school_address');
            $table->string('school_zip');

            // program preference
            $table->string('choice_first');
            $table->string('choice_second');

            // application details
            $table->string('or_number')->nullable();
            $table->string('applicant_status')->nullable();
            $table->date('exam_schedule_date')->nullable();
            $table->time('exam_schedule_time')->nullable();

            // signatories
            $table->string('certifier_name')->nullable();
            $table->string('certifier_designation')->nullable();
            $table->string('verifier_name')->nullable();
            $table->string('verifier_designation')->nullable();

            // visibility
            $table->boolean('visibility')->default(1)->comment('1 = visible, 0 = hidden');

            // status
            $table->tinyInteger('status')->default(0)->comment('0 = unpaid, 1 = paid, 2 = scheduled, 3 = passed, 4 = enrolled');

            // active
            $table->boolean('is_active')->default(1)->comment('1 = active, 0 = inactive');

            // audit columns (foreign keys to users)
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('cascade');

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
        Schema::dropIfExists('admission_shs');
    }
}
