<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'username' => 'admin1',
                'password' => Hash::make('adminpass'),
                'role' => 'admin',
                'name' => 'Admin Ruwan',
                'email' => 'admin1@gmail.com',
                'phone_number' => '0123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'username' => 'customer1',
                'password' => Hash::make('custpass1'),
                'role' => 'customer',
                'name' => 'Nimal Perera',
                'email' => 'nimal@gmail.com',
                'phone_number' => '0723456780',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'username' => 'customer2',
                'password' => Hash::make('custpass2'),
                'role' => 'customer',
                'name' => 'Kumari Silva',
                'email' => 'kumari@gmail.com',
                'phone_number' => '0724356789',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'username' => 'customer3',
                'password' => Hash::make('custpass3'),
                'role' => 'customer',
                'name' => 'Sunil Fernando',
                'email' => 'sunil@gmail.com',
                'phone_number' => '0723457878',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'username' => 'customer4',
                'password' => Hash::make('custpass4'),
                'role' => 'customer',
                'name' => 'Chathurika Senanayake',
                'email' => 'chathu@gmail.com',
                'phone_number' => '0723743789',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
