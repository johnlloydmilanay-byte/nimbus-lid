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
            $table->boolean('exam_taken')->default(0)->after('exam_schedule_time');
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
            $table->dropColumn(['exam_taken']);
        });
    }
};
