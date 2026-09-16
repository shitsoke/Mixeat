<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->string('badge')->nullable();
            $table->timestamps();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('opening_hours');
            $table->string('distance');
            $table->timestamps();
        });

        Schema::create('branch_product', function (Blueprint $table) {
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->boolean('available')->default(true);
            $table->timestamps();
            $table->primary(['branch_id', 'product_id']);
        });

        $products = [
            [1, 'Chicken Meal', 'Food Trays', 'Delicious and satisfying chicken meal.', 149, 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=900&q=80', 'Featured'],
            [2, 'Crispy Chicken', 'Food Trays', 'Crunchy, juicy, and packed with flavor.', 129, 'https://images.unsplash.com/photo-1562967916-eb82221dfb92?auto=format&fit=crop&w=900&q=80', 'Best Seller'],
            [3, 'Classic Burger', 'Food Trays', 'A savory burger stacked with classic toppings.', 135, 'https://images.unsplash.com/photo-1550547660-d9450f859349?auto=format&fit=crop&w=900&q=80', 'Popular'],
            [4, 'Cheesy Burger', 'Food Trays', 'Loaded with melted cheese and rich sauce.', 155, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=900&q=80', 'Hot Pick'],
            [5, 'Carbonara', 'Food Bowls', 'Creamy pasta with savory and comforting flavors.', 169, 'https://images.unsplash.com/photo-1555949258-eb67b1ef0ceb?auto=format&fit=crop&w=900&q=80', 'Featured'],
            [6, 'French Fries', 'Food Trays', 'Golden and crispy side that completes any meal.', 79, 'https://images.unsplash.com/photo-1576107232684-1279f390859f?auto=format&fit=crop&w=900&q=80', null],
            [7, 'Iced Tea', 'Food Bowls', 'Refreshing iced tea served chilled.', 45, 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=900&q=80', null],
            [8, 'Soft Drink', 'Food Bowls', 'Classic fizzy drink with a cool finish.', 35, 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=900&q=80', null],
            [9, 'Chocolate Cake', 'Food Bowls', 'Rich chocolate cake for a sweet ending.', 120, 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=900&q=80', 'Sweet Treat'],
        ];

        foreach ($products as [$id, $name, $category, $description, $price, $image, $badge]) {
            DB::table('products')->insert([
                'id' => $id, 'name' => $name, 'category' => $category, 'description' => $description,
                'price' => $price, 'image' => $image, 'badge' => $badge,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $branches = [
            [1, 'MixEat Banilad', 'Banilad, Cebu City', '8:00 AM - 9:00 PM', '1.2 km away'],
            [2, 'MixEat Mandaue', 'M. C. Briones St., Mandaue City', '9:00 AM - 10:00 PM', '3.4 km away'],
            [3, 'MixEat Talamban', 'Talamban, Cebu City', '7:30 AM - 9:30 PM', '5.1 km away'],
        ];

        foreach ($branches as [$id, $name, $address, $openingHours, $distance]) {
            DB::table('branches')->insert([
                'id' => $id, 'name' => $name, 'address' => $address, 'opening_hours' => $openingHours,
                'distance' => $distance, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        foreach (range(1, 3) as $branchId) {
            foreach (range(1, 9) as $productId) {
                DB::table('branch_product')->insert([
                    'branch_id' => $branchId,
                    'product_id' => $productId,
                    'available' => !($branchId === 1 && $productId === 4),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_product');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('products');
    }
};
