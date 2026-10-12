<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')
            ->where('category', 'Food Bowls')
            ->whereIn('name', ['Iced Tea', 'Soft Drink'])
            ->update(['category' => 'Drinks']);

        DB::table('products')
            ->where('category', 'Food Bowls')
            ->where('name', 'Chocolate Cake')
            ->update(['category' => 'Desserts']);
    }

    public function down(): void
    {
        DB::table('products')
            ->where('category', 'Drinks')
            ->whereIn('name', ['Iced Tea', 'Soft Drink'])
            ->update(['category' => 'Food Bowls']);

        DB::table('products')
            ->where('category', 'Desserts')
            ->where('name', 'Chocolate Cake')
            ->update(['category' => 'Food Bowls']);
    }
};
