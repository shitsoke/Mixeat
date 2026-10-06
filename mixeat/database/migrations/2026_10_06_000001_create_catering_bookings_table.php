<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catering_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('client_name', 120);
            $table->string('email');
            $table->string('phone', 30);
            $table->date('event_date')->index();
            $table->string('service_type', 50);
            $table->string('package_preference')->nullable();
            $table->unsignedSmallInteger('guest_count');
            $table->text('special_requests')->nullable();
            $table->string('campaign_source', 100)->default('direct');
            $table->string('status', 40)->default('Pending Review')->index();
            $table->string('payment_status', 40)->default('Unpaid');
            $table->decimal('quote_amount', 10, 2)->nullable();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_bookings');
    }
};
