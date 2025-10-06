<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Fruits',
                'description' => 'Fresh and organic fruits',
                'color' => 'green',
                'icon' => 'fruit'
            ],
            [
                'name' => 'Vegetables',
                'description' => 'Fresh vegetables and greens',
                'color' => 'green',
                'icon' => 'vegetable'
            ],
            [
                'name' => 'Dairy',
                'description' => 'Milk, cheese, and dairy products',
                'color' => 'blue',
                'icon' => 'dairy'
            ],
            [
                'name' => 'Meat',
                'description' => 'Fresh meat and poultry',
                'color' => 'red',
                'icon' => 'meat'
            ],
            [
                'name' => 'Bakery',
                'description' => 'Bread, cakes, and baked goods',
                'color' => 'orange',
                'icon' => 'bakery'
            ],
            [
                'name' => 'Beverages',
                'description' => 'Drinks, juices, and beverages',
                'color' => 'purple',
                'icon' => 'beverage'
            ],
            [
                'name' => 'Snacks',
                'description' => 'Chips, cookies, and snacks',
                'color' => 'yellow',
                'icon' => 'snack'
            ],
            [
                'name' => 'Household',
                'description' => 'Cleaning and household items',
                'color' => 'grey',
                'icon' => 'household'
            ]
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }
    }
}