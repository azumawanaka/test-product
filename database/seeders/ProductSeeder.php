<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Product::truncate();

        Product::factory()->create([
            'name' => fake()->colorName(),
            'descriptions' => fake()->sentence(),
        ]);
        Product::factory()->create([
            'name' => fake()->colorName(),
            'descriptions' => fake()->sentence(),
        ]);
    }
}
