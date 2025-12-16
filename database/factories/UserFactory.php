<?php

namespace Database\Factories;

use App\Enum\AgentTypeEnum;
use App\Enum\GenderEnum;
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
        $phone_number = fake()->phoneNumber();
        return [
            'status_id'    => 1,
            'name'    => fake()->name(),
            'username' => $username,
            'email' =>  $username . "@mailinator.com",
            'avatar_id'    => null,
            'gender'    => fake()->randomElement(array_column(GenderEnum::cases(), 'value')),
            'type'    => fake()->randomElement(array_column(AgentTypeEnum::cases(), 'value')),
            'doj'   => fake()->date(),
            'phone' => $phone_number,
            'numeric_code' => $phone_number ? fake()->randomElement(['1', '44', '91', '61', '81']) : null,
            'iso_code' => $phone_number ? fake()->randomElement(['US', 'GB', 'IN', 'AU', 'JP']) : null,
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
