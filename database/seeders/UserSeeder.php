<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'super@admin.com'],
            [
                'status_id'    => 1,
                'name'    => fake()->name(),
                'username' => 'superadmin',
                'email' =>  "super@mailinator.com",
                'avatar_id'    => null,
                'gender'    => 'M',
                'dob'   => fake()->date(),
                'phone' => fake()->phoneNumber(),
            ]
        );
        $superAdmin->assignRole('Super Admin');
        
        // Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'status_id'    => 1,
                'name'    => fake()->name(),
                'username' => 'admin',
                'email' =>  "admin@mailinator.com",
                'avatar_id'    => null,
                'gender'    => 'M',
                'dob'   => fake()->date(),
                'phone' => fake()->phoneNumber(),
            ]
        );
        $admin->assignRole('Admin');

        // Create 13 Agent users
        User::factory()->count(13)->create()->each(function ($user) {
            $user->assignRole('Agent');
        });
    }
}