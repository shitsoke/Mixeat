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
        Schema::create('catering_packages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 80)->unique();
            $table->string('name', 160);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('min_guests')->nullable();
            $table->unsignedInteger('max_guests')->nullable();
            $table->json('included_dishes')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('packed_meal_packages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 80)->unique();
            $table->string('name', 160);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->json('included_dishes')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('party_trays', function (Blueprint $table) {
            $table->id();
            $table->string('name', 160)->unique();
            $table->text('description')->nullable();
            $table->decimal('full_price', 10, 2);
            $table->decimal('half_price', 10, 2);
            $table->json('sizes')->nullable();
            $table->json('dishes')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        $now = now();
        $packages = [];
        foreach (CateringPackageCatalog::PACKAGES as $code => $package) {
            $packages[] = [
                'code' => $code, 'name' => $package['label'], 'description' => 'Choose '.$package['meat_count'].' meat dishes, plus one dish from each other category.',
                'price' => $package['price'], 'min_guests' => null, 'max_guests' => null,
                'included_dishes' => json_encode(['meat_count' => $package['meat_count'], 'categories' => ['vegetables', 'soup', 'pasta', 'dessert', 'rice', 'drinks']]),
                'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
            ];
        }
        DB::table('catering_packages')->insert($packages);

        $meals = [];
        foreach (CateringPackageCatalog::PACKED_MEALS as $code => $name) {
            $meals[] = [
                'code' => $code, 'name' => $name, 'description' => 'Packed meal with your choice of Bam-i or Vegetables.',
                'price' => CateringPackageCatalog::PACKED_MEAL_PRICE,
                'included_dishes' => json_encode(['main_dish' => $name, 'sides' => CateringPackageCatalog::PACKED_MEAL_SIDES, 'fixed' => CateringPackageCatalog::PACKED_MEAL_FIXED_INCLUSIONS]),
                'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
            ];
        }
        DB::table('packed_meal_packages')->insert($meals);

        $trays = [];
        foreach (CateringPackageCatalog::PARTY_TRAYS as $name => $tray) {
            $trays[] = [
                'name' => $name, 'description' => 'Choose a dish and tray size for your event.',
                'full_price' => $tray['full_price'], 'half_price' => $tray['half_price'],
                'sizes' => json_encode(['full' => 'Full', 'half' => 'Half']), 'dishes' => json_encode($tray['dishes']),
                'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
            ];
        }
        DB::table('party_trays')->insert($trays);
    }

    public function down(): void
    {
        Schema::dropIfExists('party_trays');
        Schema::dropIfExists('packed_meal_packages');
        Schema::dropIfExists('catering_packages');
    }
};
