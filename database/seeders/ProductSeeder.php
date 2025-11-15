<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'iPhone 14 Pro',
                'description' => 'Latest iPhone with advanced camera system',
                'price' => 999.99,
                'stock' => 50,
                'sku' => 'IPH14PRO-256',
                'category' => 'Electronics',
                'status' => 'active',
                'specifications' => [
                    'storage' => '256GB',
                    'color' => 'Space Black',
                    'screen' => '6.1 inch'
                ],
                'images' => [
                    ['path' => 'products/iphone-front.jpg', 'is_primary' => true],
                    ['path' => 'products/iphone-back.jpg', 'is_primary' => false],
                    ['path' => 'products/iphone-side.jpg', 'is_primary' => false]
                ]
            ],
            [
                'name' => 'MacBook Air M2',
                'description' => 'Powerful laptop with M2 chip',
                'price' => 1199.99,
                'stock' => 25,
                'sku' => 'MBA-M2-512',
                'category' => 'Electronics',
                'status' => 'active',
                'specifications' => [
                    'storage' => '512GB',
                    'memory' => '8GB',
                    'color' => 'Silver'
                ],
                'images' => [
                    ['path' => 'products/macbook-front.jpg', 'is_primary' => true],
                    ['path' => 'products/macbook-keyboard.jpg', 'is_primary' => false],
                    ['path' => 'products/macbook-side.jpg', 'is_primary' => false]
                ]
            ],
            [
                'name' => 'Wireless Headphones',
                'description' => 'Noise cancelling wireless headphones',
                'price' => 299.99,
                'stock' => 0,
                'sku' => 'WH-1000XM4',
                'category' => 'Audio',
                'status' => 'out_of_stock',
                'specifications' => [
                    'battery' => '30 hours',
                    'connectivity' => 'Bluetooth 5.0',
                    'color' => 'Black'
                ],
                'images' => [
                    ['path' => 'products/headphones-front.jpg', 'is_primary' => true],
                    ['path' => 'products/headphones-case.jpg', 'is_primary' => false]
                ]
            ],
            [
                'name' => 'Gaming Monitor',
                'description' => '27-inch 4K gaming monitor',
                'price' => 499.99,
                'stock' => 15,
                'sku' => 'GM-27-4K',
                'category' => 'Electronics',
                'status' => 'active',
                'specifications' => [
                    'size' => '27 inch',
                    'resolution' => '4K UHD',
                    'refresh_rate' => '144Hz'
                ],
                'images' => [
                    ['path' => 'products/monitor-front.jpg', 'is_primary' => true],
                    ['path' => 'products/monitor-back.jpg', 'is_primary' => false],
                    ['path' => 'products/monitor-setup.jpg', 'is_primary' => false]
                ]
            ],
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'RGB mechanical gaming keyboard',
                'price' => 129.99,
                'stock' => 30,
                'sku' => 'MK-RGB-BLUE',
                'category' => 'Accessories',
                'status' => 'active',
                'specifications' => [
                    'switch_type' => 'Blue switches',
                    'backlight' => 'RGB',
                    'layout' => 'US QWERTY'
                ],
                'images' => [
                    ['path' => 'products/keyboard-front.jpg', 'is_primary' => true],
                    ['path' => 'products/keyboard-rgb.jpg', 'is_primary' => false],
                    ['path' => 'products/keyboard-closeup.jpg', 'is_primary' => false]
                ]
            ]
        ];

        // Clear existing data - Use forceDelete for soft deletes
        ProductImage::query()->forceDelete();
        Product::query()->forceDelete();

        foreach ($products as $productData) {
            // Create product
            $product = Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'sku' => $productData['sku'],
                'category' => $productData['category'],
                'status' => $productData['status'],
                'specifications' => $productData['specifications']
            ]);

            // Create product images
            foreach ($productData['images'] as $index => $imageData) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imageData['path'],
                    'is_primary' => $imageData['is_primary'],
                    'sort_order' => $index
                ]);
            }
        }
    }
}