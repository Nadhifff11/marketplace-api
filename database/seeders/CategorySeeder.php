<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'slug' => 'elektronik'],
            ['name' => 'Fashion', 'slug' => 'fashion'],
            ['name' => 'Olahraga', 'slug' => 'olahraga'],
            ['name' => 'Kesehatan & Kecantikan', 'slug' => 'kesehatan-kecantikan'],
            ['name' => 'Rumah Tangga', 'slug' => 'rumah-tangga'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}