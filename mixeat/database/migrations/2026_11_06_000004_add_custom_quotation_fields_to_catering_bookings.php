<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catering_bookings', function (Blueprint $table) {
            $table->string('quotation_status', 32)->nullable()->index();
            $table->unsignedInteger('quotation_version')->default(0);
            $table->text('quotation_notes')->nullable();
            $table->timestamp('quotation_sent_at')->nullable();
            $table->timestamp('quotation_accepted_at')->nullable();
            $table->timestamp('quotation_declined_at')->nullable();
            $table->foreignId('quotation_sent_by')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('catering_quotation_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catering_booking_id')->constrained('catering_bookings')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->decimal('amount', 12, 2);
            $table->text('notes')->nullable();
            $table->string('status', 24)->default('Sent');
            $table->timestamp('sent_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['catering_booking_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_quotation_versions');
        Schema::table('catering_bookings', function (Blueprint $table) {
            $table->dropForeign(['quotation_sent_by']);
            $table->dropColumn([
                'quotation_status',
                'quotation_version',
                'quotation_notes',
                'quotation_sent_at',
                'quotation_accepted_at',
                'quotation_declined_at',
                'quotation_sent_by',
            ]);
        });
    }
};
