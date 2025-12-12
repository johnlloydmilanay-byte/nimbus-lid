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
        Schema::table('srm_programs', function (Blueprint $table) {
            $table->float('api')->after('is_active');
            $table->string('prereq')->nullable()->after('api');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('srm_programs', function (Blueprint $table) {
            $table->dropColumn(['api', 'prereq']);
        });
    }
};
