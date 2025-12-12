<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admission_college', function (Blueprint $table) {
            // Drop old string column
            if (Schema::hasColumn('admission_college', 'strand')) {
                $table->dropColumn('strand');
            }

            // Add new foreign key column
            $table->unsignedBigInteger('strand_id')->nullable()->after('id');

            // Optional: Add foreign key constraint
            $table->foreign('strand_id')
                ->references('id')
                ->on('admission_college_shs_programs')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('admission_college', function (Blueprint $table) {
            // Drop foreign key and strand_id column
            $table->dropForeign(['strand_id']);
            $table->dropColumn('strand_id');

            // Restore old strand column
            $table->string('strand')->nullable();
        });
    }
};
