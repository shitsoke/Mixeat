<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('label', 100)->nullable();
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 2)->nullable();
            $table->string('image');
            $table->string('button_text', 60)->nullable();
            $table->string('button_url', 2048)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        $settings = DB::table('site_settings')
            ->whereIn('key', ['promotion_image', 'promotion_label', 'promotion_title', 'promotion_price'])
            ->pluck('value', 'key');

        if (! empty($settings['promotion_title']) || ! empty($settings['promotion_image'])) {
            DB::table('promotions')->insert([
                'label' => $settings['promotion_label'] ?? null,
                'title' => $settings['promotion_title'] ?? 'Featured promotion',
                'description' => '',
                'price' => isset($settings['promotion_price']) && $settings['promotion_price'] !== ''
                    ? $settings['promotion_price']
                    : null,
                'image' => $settings['promotion_image'] ?? '',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
