<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing products to avoid duplicates
        DB::table('products')->truncate();
        
        DB::table('products')->insert([
            ['id' => 1, 'name' => 'Carrots', 'description' => 'Fresh Sri Lankan carrots', 'price' => 120.00, 'category' => 'Vegetables', 'quantity' => '1 kg', 'image' => 'carrots.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Beans', 'description' => 'Green beans from Nuwara Eliya', 'price' => 90.00, 'category' => 'Vegetables', 'quantity' => '500 g', 'image' => 'beans.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Leeks', 'description' => 'Organic leeks', 'price' => 110.00, 'category' => 'Vegetables', 'quantity' => '1 kg', 'image' => 'leeks.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Brinjals', 'description' => 'Purple brinjals from local farms', 'price' => 130.00, 'category' => 'Vegetables', 'quantity' => '1 kg', 'image' => 'brinjals.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Tomatoes', 'description' => 'Juicy red tomatoes', 'price' => 100.00, 'category' => 'Vegetables', 'quantity' => '1 kg', 'image' => 'tomatoes.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Bananas', 'description' => 'Kolikuttu bananas', 'price' => 85.00, 'category' => 'Fruits', 'quantity' => '1 kg', 'image' => 'bananas.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'Mangoes', 'description' => 'Sweet juicy mangoes', 'price' => 150.00, 'category' => 'Fruits', 'quantity' => '1 kg', 'image' => 'mangoes.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => 'Papaya', 'description' => 'Ripe papaya for juice or salad', 'price' => 130.00, 'category' => 'Fruits', 'quantity' => '1', 'image' => 'papaya.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'name' => 'Pineapple', 'description' => 'Freshly cut pineapple', 'price' => 160.00, 'category' => 'Fruits', 'quantity' => '1', 'image' => 'pineapple.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'name' => 'Beef', 'description' => 'Fresh beef from trusted sources', 'price' => 1200.00, 'category' => 'Meat', 'quantity' => '1 kg', 'image' => 'beef.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'name' => 'Pork', 'description' => 'Locally sourced pork', 'price' => 1100.00, 'category' => 'Meat', 'quantity' => '1 kg', 'image' => 'pork.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'name' => 'Anchor Milk Powder', 'description' => 'Full cream milk powder', 'price' => 940.00, 'category' => 'Dairy Products', 'quantity' => '400 g', 'image' => 'anchor_milk.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'name' => 'Kotmale Cheese', 'description' => 'Cheese Wedges', 'price' => 600.00, 'category' => 'Dairy Products', 'quantity' => '1', 'image' => 'cheese.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'name' => 'Highland Butter', 'description' => 'Unsalted butter', 'price' => 750.00, 'category' => 'Dairy Products', 'quantity' => '200 g', 'image' => 'butter.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'name' => 'Fresh Curd', 'description' => 'Buffalo milk curd', 'price' => 300.00, 'category' => 'Dairy Products', 'quantity' => '1 L', 'image' => 'curd.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'name' => 'Yoghurt', 'description' => 'Kotmale vanilla yoghurt', 'price' => 50.00, 'category' => 'Dairy Products', 'quantity' => '1', 'image' => 'yoghurt.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'name' => 'Samba Rice', 'description' => 'White samba rice', 'price' => 190.00, 'category' => 'Grains', 'quantity' => '1 kg', 'image' => 'samba_rice.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'name' => 'Nadu Rice', 'description' => 'White nadu rice', 'price' => 180.00, 'category' => 'Grains', 'quantity' => '1 kg', 'image' => 'nadu_rice.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'name' => 'Dhal', 'description' => 'Red lentils', 'price' => 160.00, 'category' => 'Grains', 'quantity' => '500 g', 'image' => 'dhal.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'name' => 'Green Gram', 'description' => 'Whole green mung beans', 'price' => 210.00, 'category' => 'Grains', 'quantity' => '500 g', 'image' => 'green_gram.jpg', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
