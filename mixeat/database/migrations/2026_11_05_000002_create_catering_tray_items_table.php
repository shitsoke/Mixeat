<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catering_tray_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catering_booking_id')->constrained('catering_bookings')->cascadeOnDelete();
            $table->string('category', 40);
            $table->string('dish', 160);
            $table->string('tray_size', 10);
            $table->unsignedSmallInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->index(['catering_booking_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_tray_items');
    }
};
