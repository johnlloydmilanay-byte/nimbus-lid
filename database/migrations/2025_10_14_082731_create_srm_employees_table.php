
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
        Schema::create('srm_employees', function (Blueprint $table) {
            // basic information

            $table->bigIncrements('id');
            $table->string('employee_id', 50)->unique();
            $table->string('tk_id', 50)->unique()->nullable();

            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('suffix')->nullable();
            $table->string('prefix')->nullable();
            $table->string('extension')->nullable();

            // work information
            $table->unsignedBigInteger('department_id');
            $table->string('designation')->nullable();

            $table->unsignedBigInteger('position_id');
            $table->date('employment_date');
            $table->unsignedBigInteger('rank_faculty_id')->nullable();

            $table->unsignedBigInteger('employee_type_id');
            $table->unsignedBigInteger('employment_status_id');

            // ---------------------------------- //

            $table->boolean('is_active')->default(1)->comment('1 = active, 0 = inactive');
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('srm_employees', function (Blueprint $table) {
            $table->foreign('employee_id')->references('user_id')->on('srm_users')->onDelete('cascade');

            $table->foreign('department_id')->references('id')->on('sys_departments')->onDelete('cascade');
            $table->foreign('position_id')->references('id')->on('sys_employee_position')->onDelete('cascade');
            $table->foreign('rank_faculty_id')->references('id')->on('sys_employee_rank')->onDelete('cascade');
            $table->foreign('employee_type_id')->references('id')->on('sys_employee_type')->onDelete('cascade');
            $table->foreign('employment_status_id')->references('id')->on('sys_employee_status')->onDelete('cascade');

            $table->foreign('created_by')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('srm_users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('user_id')->on('srm_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('srm_employees');
    }
};
