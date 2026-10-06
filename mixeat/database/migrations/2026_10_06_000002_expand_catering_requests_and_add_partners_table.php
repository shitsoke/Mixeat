<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catering_bookings', function (Blueprint $table) {
            $table->string('company_name', 160)->nullable()->after('phone');
            $table->time('event_time')->nullable()->after('event_date');
            $table->string('venue', 255)->nullable()->after('event_time');
            $table->string('event_type', 50)->nullable()->after('venue');
            $table->string('budget_range', 100)->nullable()->after('guest_count');
        });

        DB::table('catering_bookings')->where('status', 'Pending Review')->update(['status' => 'New']);

        Schema::create('catering_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('logo_path')->nullable();
            $table->string('logo_url', 2048)->nullable();
            $table->string('website_url', 2048)->nullable();
            $table->boolean('is_visible')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        $legacyPartners = DB::table('site_settings')
            ->where('key', 'catering_partners')
            ->value('value');

        $partners = json_decode($legacyPartners ?: '[]', true) ?: [];
        if ($partners === []) {
            $partners = [
                ['name' => 'Hospital Logo'],
                ['name' => 'Gov Agency Logo'],
                ['name' => 'Corporate Logo'],
                ['name' => 'University Logo'],
            ];
        }

        foreach ($partners as $index => $partner) {
            if (empty($partner['name'])) {
                continue;
            }

            DB::table('catering_partners')->insert([
                'name' => mb_substr($partner['name'], 0, 120),
                'logo_url' => $partner['logo_url'] ?? null,
                'is_visible' => true,
                'sort_order' => $index,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_partners');
        Schema::table('catering_bookings', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'event_time', 'venue', 'event_type', 'budget_range']);
        });
    }
};
