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
        Schema::table('sys_departments', function (Blueprint $table) {
            $table->string('emp_id')->nullable()->after('name');

            $table->foreign('emp_id')
                ->references('employee_id')
                ->on('srm_employees')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sys_departments', function (Blueprint $table) {
            $table->dropForeign(['emp_id']);
            $table->dropColumn('emp_id');
        });
    }
};
