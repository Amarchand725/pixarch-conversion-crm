<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Nette\Utils\Random;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCredentialsMail;
use Illuminate\Support\Facades\Log;

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

        $users = [
            [
                'status_id' => 1,
                "name" => "Rizwan Naeem Sheikh",
                'username' => Str::slug("Rizwan Naeem Sheikh"),
                'phone' => fake()->phoneNumber(),
                "email" => "rizwan@100keys.ae",
                'role' => 'ADMIN',
                'gender' => 'M',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
        ];

        foreach ($users as $data) {
            if($data['email'] !== null && $data['email'] !== ''){
                $role = $data['role'];
                unset($data['role']);

                // generate plain password FIRST
                $plainPassword = Str::random(8);

                $data['password'] = Hash::make($plainPassword);
                $data['type'] = 'auto_assigned';
                $data['daily_capacity'] = 10;

                $model = User::updateOrCreate(
                    ['email' => $data['email']],
                    $data
                );

                // assign role
                if ($role === 'ADMIN') {
                    $model->assignRole('Admin');
                } else {
                    $model->assignRole('Agent');
                }

                // send email safely
                // if (!empty($model->email)) {
                //     try {
                //         Mail::to($model->email)->send(
                //             new UserCredentialsMail($model, $plainPassword)
                //         );
                //     } catch (\Throwable $e) {
                //         Log::error('Credential email failed', [
                //             'user_id' => $model->id,
                //             'email' => $model->email,
                //             'error' => $e->getMessage(),
                //         ]);
                //     }
                // }
            }
        }
    }
}