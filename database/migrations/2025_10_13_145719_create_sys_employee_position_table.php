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
        Schema::create('sys_employee_position', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('employee_position');
            $table->boolean('is_active')->default(1)->comment('1 = active, 0 = inactive');
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('sys_employee_position');
    }
};
