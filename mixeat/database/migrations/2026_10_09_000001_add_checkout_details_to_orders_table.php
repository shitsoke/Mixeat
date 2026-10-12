<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'pickup_at')) {
                $table->dateTime('pickup_at')->nullable();
            }

            if (! Schema::hasColumn('orders', 'delivery_method')) {
                $table->string('delivery_method')->nullable();
            }

            if (! Schema::hasColumn('orders', 'delivery_address')) {
                $table->text('delivery_address')->nullable();
            }

            if (! Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            foreach (['pickup_at', 'delivery_method', 'delivery_address', 'payment_method'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
