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
        Schema::table('admission_jhs_subtest_result', function (Blueprint $table) {
            $table->dropColumn('api');
        });

        Schema::table('admission_jhs_subtest_result', function (Blueprint $table) {
            $table->integer('api')->after('hs_grade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admission_jhs_subtest_result', function (Blueprint $table) {
            $table->dropColumn('api');
        });

        Schema::table('admission_jhs_subtest_result', function (Blueprint $table) {
            $table->integer('api')->after('hs_grade');
        });
    }
};
