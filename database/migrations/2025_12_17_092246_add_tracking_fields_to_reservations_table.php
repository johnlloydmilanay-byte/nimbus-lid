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
        Schema::table('reservations', function (Blueprint $table) {
            // Student specific fields (if not already present)
            if (!Schema::hasColumn('reservations', 'student_id')) {
                $table->string('student_id')->nullable()->after('group_number');
            }
            
            if (!Schema::hasColumn('reservations', 'email')) {
                $table->string('email')->nullable()->after('student_id');
            }
            
            if (!Schema::hasColumn('reservations', 'contact_number')) {
                $table->string('contact_number')->nullable()->after('email');
            }

            // Item tracking fields
            if (!Schema::hasColumn('reservations', 'released_to_borrower')) {
                $table->boolean('released_to_borrower')->default(0)->after('status');
            }
            
            if (!Schema::hasColumn('reservations', 'released_date')) {
                $table->date('released_date')->nullable()->after('released_to_borrower');
            }
            
            if (!Schema::hasColumn('reservations', 'released_by')) {
                $table->string('released_by')->nullable()->after('released_date');
            }
            
            if (!Schema::hasColumn('reservations', 'returned_by_borrower')) {
                $table->boolean('returned_by_borrower')->default(0)->after('released_by');
            }
            
            if (!Schema::hasColumn('reservations', 'return_date')) {
                $table->date('return_date')->nullable()->after('returned_by_borrower');
            }
            
            if (!Schema::hasColumn('reservations', 'received_by')) {
                $table->string('received_by')->nullable()->after('return_date');
            }
            
            if (!Schema::hasColumn('reservations', 'notes')) {
                $table->text('notes')->nullable()->after('received_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'student_id',
                'email',
                'contact_number',
                'released_to_borrower',
                'released_date',
                'released_by',
                'returned_by_borrower',
                'return_date',
                'received_by',
                'notes'
            ]);
        });
    }
};