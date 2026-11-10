<?php

use App\Services\CateringPackageCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catering_menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 160);
            $table->text('description')->nullable();
            $table->string('category', 30);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['category', 'name']);
        });

        $now = now();
        $items = [];
        $categories = [
            'meat' => CateringPackageCatalog::meatItems(),
            'vegetables' => CateringPackageCatalog::MENU['vegetables']['items'],
            'rice' => CateringPackageCatalog::MENU['rice']['items'],
            'pasta' => CateringPackageCatalog::MENU['pasta']['items'],
            'soup' => CateringPackageCatalog::MENU['soup']['items'],
            'dessert' => CateringPackageCatalog::MENU['dessert']['items'],
            'drinks' => CateringPackageCatalog::MENU['drinks']['items'],
        ];

        foreach ($categories as $category => $names) {
            foreach (array_unique($names) as $name) {
                $items[] = [
                    'name' => $name,
                    'description' => null,
                    'category' => $category,
                    'image' => null,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('catering_menu_items')->insert($items);
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_menu_items');
    }
};
