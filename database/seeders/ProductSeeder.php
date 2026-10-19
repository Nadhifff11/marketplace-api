<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Buat 1 user seller dummy
        $seller = User::create([
            'name' => 'Toko Sukses',
            'email' => 'seller@example.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
        ]);

        // Buat 1 store dummy
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Toko Sukses Jaya',
            'slug' => 'toko-sukses-jaya',
            'description' => 'Toko elektronik dan fashion terpercaya',
        ]);

        $categoryElektronik = Category::where('slug', 'elektronik')->first();
        $categoryFashion = Category::where('slug', 'fashion')->first();

        $products = [
            [
                'store_id' => $store->id,
                'category_id' => $categoryElektronik->id,
                'name' => 'Sepatu Running Nike Air',
                'slug' => 'sepatu-running-nike-air',
                'description' => 'Sepatu lari nyaman untuk olahraga harian',
                'price' => 850000,
                'stock' => 25,
            ],
            [
                'store_id' => $store->id,
                'category_id' => $categoryFashion->id,
                'name' => 'Kaos Polos Premium',
                'slug' => 'kaos-polos-premium',
                'description' => 'Kaos bahan cotton combed 30s',
                'price' => 75000,
                'stock' => 100,
            ],
            [
                'store_id' => $store->id,
                'category_id' => $categoryElektronik->id,
                'name' => 'Headset Bluetooth Wireless',
                'slug' => 'headset-bluetooth-wireless',
                'description' => 'Headset bluetooth dengan kualitas suara jernih',
                'price' => 250000,
                'stock' => 40,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}