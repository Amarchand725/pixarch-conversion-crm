<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {   
        // Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'status_id'    => 1,
                'name'    => fake()->name(),
                'username' => 'admin',
                'email' =>  "admin@gmail.com",
                'password' =>  Hash::make('admin@123'),
                'avatar_id'    => null,
                'gender'    => 'M',
                'doj'   => fake()->date(),
                'phone' => fake()->phoneNumber(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );
        $admin->assignRole('Admin');

        // Create 3 Lead users
        User::factory()->count(3)->create()->each(function ($user) {
            $user->assignRole('Lead');
        });

        // Create 10 Agent users
        User::factory()->count(10)->create()->each(function ($user) {
            $user->assignRole('Agent');
        });
    }
}