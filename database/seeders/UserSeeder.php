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
                "phone" => "+971568991127",
                "email" => "rizwan@100keys.ae",
                'role' => 'ADMIN',
                'gender' => 'M',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "SHAHRUKH SALMAN QAYYUM SALMAN ZUBERI",
                'username' => Str::slug("SHAHRUKH SALMAN QAYYUM SALMAN ZUBERI"),
                "phone" => "+971588019773",
                "email" => "shahrukh@100keys.ae",
                'role' => 'ADMIN',
                'gender' => 'M',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "ANGELITA ROMERO HIPOLITO",
                'username' => Str::slug("ANGELITA ROMERO HIPOLITO"),
                "phone" => "+971561992713",
                "email" => "admin@100keys.ae",
                'role' => 'AGENT',
                'gender' => 'F',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "HAMMAD UR REHMAN TAHMURAS IFZAL KIYANI",
                'username' => Str::slug("HAMMAD UR REHMAN TAHMURAS IFZAL KIYANI"),
                "phone" => "+971566884381",
                "email" => "hammad@100keys.ae",
                'role' => 'ADMIN',
                'gender' => 'M',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "ZAKI ASIF MUHAMMAD ASIF SHEHZAD",
                'username' => Str::slug("ZAKI ASIF MUHAMMAD ASIF SHEHZAD"),
                "phone" => "+971522205168",
                "email" => "zaki@100keys.ae",
                'role' => 'AGENT',
                'gender' => 'M',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "HAFIZA MUSHYYEN HAMMAD",
                'username' => Str::slug("HAFIZA MUSHYYEN HAMMAD"),
                "phone" => "+971522738402",
                "email" => "mushyyen@100keys.ae",
                'role' => 'AGENT',
                'gender' => 'F',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "HAMMAD SHAUKAT HUSSAIN",
                'username' => Str::slug("HAMMAD SHAUKAT HUSSAIN"),
                "phone" => "+971522743790",
                "email" => "khatri@100keys.ae",
                'role' => 'AGENT',
                'gender' => 'M',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "ADAM ASIF ASIF SHERAFUDEEN",
                'username' => Str::slug("ADAM ASIF ASIF SHERAFUDEEN"),
                "phone" => "+971528186797",
                "email" => "adam@100keys.ae",
                'role' => 'AGENT',
                'gender' => 'M',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "SADAF KHAN HAIDER HUSSAIN KHAN",
                'username' => Str::slug("SADAF KHAN HAIDER HUSSAIN KHAN"),
                "phone" => "+971504551766",
                "email" => "sadaf@100keys.ae",
                'role' => 'AGENT',
                'gender' => 'F',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "FARMEEN ASHAR KHAN",
                'username' => Str::slug("FARMEEN ASHAR KHAN"),
                "phone" => "+971502160104",
                "email" => "farmeen@100keys.ae",
                'role' => 'AGENT',
                'gender' => 'F',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "HASSAN ALI ABASSI",
                'username' => Str::slug("HASSAN ALI ABASSI"),
                "phone" => "+971525305643",
                "email" => "hassan@100keys.ae",
                'role' => 'AGENT',
                'gender' => 'M',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "AREEL UR REHMAN HAFEEZ",
                'username' => Str::slug("AREEL UR REHMAN HAFEEZ"),
                "phone" => "+971528269026",
                "email" => "areel@100keys.ae",
                'role' => 'AGENT',
                'gender' => 'M',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "NIDA BHADELIA",
                'username' => Str::slug("NIDA BHADELIA"),
                "phone" => "+971526329603",
                "email" => "nida@100keys.ae",
                'role' => 'AGENT',
                'gender' => 'F',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "Natallia Varabyova",
                'username' => Str::slug("Natallia Varabyova"),
                "phone" => "+971554001600",
                "email" => "nv@100keys.ae",
                'role' => 'AGENT',
                'gender' => 'F',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "Khaled hossan ( SIM I USING BY DIVYA )",
                'username' => Str::slug("Khaled hossan ( SIM I USING BY DIVYA )"),
                "phone" => "+971564336705",
                "email" => "khaled@100keys.ae",
                'role' => 'AGENT',
                'gender' => 'M',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "Khawaja Umair",
                'username' => Str::slug("Khawaja Umair"),
                "phone" => "+923082288625",
                "email" => "digital@100keys.ae",
                'role' => 'AGENT',
                'gender' => 'M',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'status_id' => 1,
                "name" => "Mujahid Ghani",
                'username' => Str::slug("Mujahid Ghani"),
                "phone" => null,
                "email" => null,
                'role' => 'AGENT',
                'gender' => 'M',
                'doj' => fake()->date(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
        ];

        foreach ($users as $data) {
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