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
        Schema::table('admission_college', function (Blueprint $table) {
            $table->integer('total_rs')->nullable()->after('is_active');
            $table->integer('total_ave_api')->nullable()->after('total_rs');
            $table->string('remarks')->nullable()->after('total_ave_api');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admission_college', function (Blueprint $table) {
            $table->dropColumn(['total_rs', 'total_ave_api', 'remarks']);
        });
    }
};
