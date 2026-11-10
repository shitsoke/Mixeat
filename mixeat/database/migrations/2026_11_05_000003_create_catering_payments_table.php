<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catering_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catering_booking_id')->constrained('catering_bookings')->cascadeOnDelete();
            $table->string('payment_type', 16);
            $table->string('payment_method', 40);
            $table->decimal('amount', 12, 2);
            $table->string('payment_reference', 64)->nullable()->index();
            $table->string('status', 40)->index();
            $table->boolean('duplicate_reference')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->decimal('verified_amount', 12, 2)->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('collected_at')->nullable();
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('verification_remarks')->nullable();
            $table->timestamps();

            $table->index(['catering_booking_id', 'payment_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_payments');
    }
};
