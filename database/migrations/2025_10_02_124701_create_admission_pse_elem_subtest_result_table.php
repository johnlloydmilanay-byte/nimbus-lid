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
        Schema::create('admission_pse_elem_subtest_result', function (Blueprint $table) {
            $table->id();

            $table->string('application_number_id');
            $table->foreign('application_number_id')
                ->references('application_number')
                ->on('admission_pse')
                ->onDelete('cascade');

            $table->string('name');
            $table->string('subtest_id');

            $table->integer('ts');
            $table->double('rs');
            $table->double('percentage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admission_pse_elem_subtest_result');
    }
};
