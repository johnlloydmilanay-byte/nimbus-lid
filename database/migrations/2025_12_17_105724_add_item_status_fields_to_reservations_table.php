<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemStatusFieldsToReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->boolean('items_released')->default(false)->after('status');
            $table->timestamp('items_released_at')->nullable()->after('items_released');
            $table->boolean('items_returned')->default(false)->after('items_released_at');
            $table->timestamp('items_returned_at')->nullable()->after('items_returned');
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
            $table->dropColumn(['items_released', 'items_released_at', 'items_returned', 'items_returned_at']);
        });
    }
}