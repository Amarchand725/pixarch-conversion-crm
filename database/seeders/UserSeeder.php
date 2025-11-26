<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\User\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->count(5)->create();
    }
}