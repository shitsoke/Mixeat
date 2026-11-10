<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catering_bookings', function (Blueprint $table) {
            $table->decimal('package_price_per_pax', 10, 2)->nullable();
            $table->json('menu_selections')->nullable();
            $table->decimal('estimated_total', 12, 2)->nullable();
            $table->uuid('submission_token')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('catering_bookings', function (Blueprint $table) {
            $table->dropUnique(['submission_token']);
            $table->dropColumn([
                'package_price_per_pax',
                'menu_selections',
                'estimated_total',
                'submission_token',
            ]);
        });
    }
};
