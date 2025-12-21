<?php

namespace Database\Factories;

use App\Enum\AgentTypeEnum;
use App\Enum\GenderEnum;
use App\Services\PhoneNumberService;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $username = fake()->username();
        $parsed = null;

        while (!$parsed) {
            try {
                // mix faker + known mobile rules
                $country = fake()->randomElement(['PK', 'US', 'IN']);

                $rawPhone = match ($country) {
                    'PK' => '+92' . fake()->numberBetween(3000000000, 3999999999),
                    'US' => '+1' . fake()->numberBetween(2000000000, 9999999999),
                    'IN' => '+91' . fake()->randomElement([6,7,8,9]) . fake()->numberBetween(100000000, 999999999),
                };

                $parsed = PhoneNumberService::parse($rawPhone);

            } catch (Exception $e) {
                $parsed = null; // retry
            }
        }

        return [
            'status_id'    => 1,
            'name'    => fake()->name(),
            'username' => $username,
            'email' =>  $username . "@mailinator.com",
            'avatar_id'    => null,
            'gender'    => fake()->randomElement(array_column(GenderEnum::cases(), 'value')),
            'type'    => fake()->randomElement(array_column(AgentTypeEnum::cases(), 'value')),
            'doj'   => fake()->date(),
            'phone' => $parsed['e164'],
            'numeric_code' => $parsed['numeric_code'],
            'iso_code' => $parsed['iso_code'],
            'daily_capacity' => fake()->numberBetween(1, 10),
            'two_factor'    => null,
            'notification'  => null,
            'password'  => 'user@321',
            'remember_token' => Str::random(10),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),

        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
