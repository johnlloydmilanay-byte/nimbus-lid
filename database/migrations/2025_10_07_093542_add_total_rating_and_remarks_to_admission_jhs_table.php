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
        Schema::table('admission_jhs', function (Blueprint $table) {
            $table->integer('total_rating')->nullable()->after('is_active');
            $table->string('remarks')->nullable()->after('total_rating');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admission_jhs', function (Blueprint $table) {
            $table->dropColumn(['total_rating', 'remarks']);
        });
    }
};
