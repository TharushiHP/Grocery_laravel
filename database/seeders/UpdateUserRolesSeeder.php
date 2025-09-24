<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update all users that don't have a role set
        \DB::table('users')->whereNull('role')->orWhere('role', '')->update([
            'role' => 'customer'
        ]);

        // Update all users that don't have a username set
        \DB::table('users')->whereNull('username')->orWhere('username', '')->update([
            'username' => \DB::raw('CONCAT("user_", id)')
        ]);

        // Update all users that don't have a phone_number set
        \DB::table('users')->whereNull('phone_number')->orWhere('phone_number', '')->update([
            'phone_number' => '0000000000'
        ]);

        // Create an admin user - update the first user to be admin
        $firstUser = \DB::table('users')->first();
        if ($firstUser) {
            \DB::table('users')->where('id', $firstUser->id)->update([
                'role' => 'admin',
                'username' => 'admin',
                'phone_number' => '0123456789'
            ]);
        }
    }
}
