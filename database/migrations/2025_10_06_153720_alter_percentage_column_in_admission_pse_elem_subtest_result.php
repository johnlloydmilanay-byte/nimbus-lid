<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admission_pse_elem_subtest_result', function (Blueprint $table) {
            $table->dropColumn('percentage');
        });

        Schema::table('admission_pse_elem_subtest_result', function (Blueprint $table) {
            $table->string('percentage')->after('rs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_pse_elem_subtest_result', function (Blueprint $table) {
            $table->dropColumn('percentage');
        });

        Schema::table('admission_pse_elem_subtest_result', function (Blueprint $table) {
            $table->double('percentage')->after('rs');
        });
    }
};
