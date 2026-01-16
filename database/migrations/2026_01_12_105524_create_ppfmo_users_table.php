<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePpfmoUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppfmo_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            
            // 18 for PPFMO-testing only
            $table->unsignedBigInteger('department_id')->default(18)->comment('ID 18 is reserved for PPFMO');
            
            // 1=department head, 2= management staff, 3= maintenance personel
            $table->unsignedBigInteger('designation_id')->comment('1: Head, 2: Mgmt, 3: Maint');
            
            $table->boolean('is_active')->default(true);
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            $table->softDeletes();
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
        Schema::dropIfExists('ppfmo_users');
    }
}