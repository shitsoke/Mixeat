<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catering_email_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catering_booking_id')->constrained()->cascadeOnDelete();
            $table->string('event_key', 191);
            $table->string('recipient_type', 20);
            $table->string('recipient_email');
            $table->string('status', 20)->default('queued');
            $table->text('failure_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['event_key', 'recipient_email']);
            $table->index(['catering_booking_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_email_deliveries');
    }
};
