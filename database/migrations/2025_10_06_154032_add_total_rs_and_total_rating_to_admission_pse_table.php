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
        Schema::table('admission_pse', function (Blueprint $table) {
            $table->integer('total_rs')->nullable()->after('is_active');
            $table->string('total_rating')->nullable()->after('total_rs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_pse', function (Blueprint $table) {
            $table->dropColumn(['total_rs', 'total_rating']);
        });
    }
};
