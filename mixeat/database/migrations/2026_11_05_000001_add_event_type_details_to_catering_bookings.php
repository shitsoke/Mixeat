<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catering_bookings', function (Blueprint $table) {
            $table->string('event_type_details', 160)->nullable()->after('event_type');
        });
    }

    public function down(): void
    {
        Schema::table('catering_bookings', function (Blueprint $table) {
            $table->dropColumn('event_type_details');
        });
    }
};
