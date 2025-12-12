<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elogs', function (Blueprint $table) {
            $table->boolean('is_used')
                  ->default(0)
                  ->after('purpose');
        });
    }

    public function down(): void
    {
        Schema::table('elogs', function (Blueprint $table) {
            $table->dropColumn('is_used');
        });
    }
};
