<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Option;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some products using factory
        Product::factory()->count(20)->create()->each(function ($product) {
            // Attach random categories
            $categories = Category::inRandomOrder()
                ->limit(rand(1, 3))
                ->pluck('id');
            $product->categories()->attach($categories);

            // Attach random options
            $options = Option::inRandomOrder()
                ->limit(rand(1, 2))
                ->pluck('id');
            $product->options()->attach($options);
        });

        // Create specific test products
        $testProducts = [
            [
                'sku' => 'TEST-001',
                'name' => 'Test Product One',
                'price' => 99.99,
                'weight' => 1.5,
                'descriptions' => 'This is a test product for API testing',
                'thumbnail' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=300&fit=crop',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&h=600&fit=crop',
                'stock' => 50,
                'create_date' => now(),
            ],
            [
                'sku' => 'TEST-002',
                'name' => 'Test Product Two',
                'price' => 49.99,
                'weight' => 0.8,
                'descriptions' => 'Another test product for validation testing',
                'thumbnail' => 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=300&h=300&fit=crop',
                'image' => 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=600&h=600&fit=crop',
                'stock' => 0, // Out of stock
                'create_date' => now(),
            ],
        ];

        foreach ($testProducts as $productData) {
            $product = Product::create($productData);
            
            // Attach to Electronics category
            $product->categories()->attach(1);
            
            // Attach some options
            $product->options()->attach([1, 2]);
        }
    }
}