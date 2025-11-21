<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'status_id'              => 1,
            'name'              => 'Admin User',
            'username'          => 'admin_user',
            'email'             => 'admin@gmail.com',
            'avatar_id'         => null,
            'gender'            => 'M',
            'dob'               => '1985-01-01',
            'phone'             => '+923001112233',
            'two_factor'        => null,
            'notification'      => null,
            'password'          => bcrypt('secret'),
            'remember_token'    => Str::random(10),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        $admin->assignRole('Admin');

        User::factory()->count(1)->create()->each(fn($user) => $user->assignRole('Super Admin'));
        User::factory()->count(1)->create()->each(fn($user) => $user->assignRole('Admin'));
        User::factory()->count(10)->create()->each(fn($user) => $user->assignRole('Agent'));
    }
}
