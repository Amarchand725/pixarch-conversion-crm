<?php
namespace Database\Factories;

use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Lead\Models\Lead;
use App\Modules\LeadCapture\Models\LeadCapture;
use App\Services\PhoneNumberService;
use Exception;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
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
            'uuid'            => $this->faker->uuid(),
            'source_id' => $this->faker->randomElement(Source::pluck('id')->toArray()),
            'lead_capture_id' => $this->faker->randomElement(LeadCapture::pluck('id')->toArray()),
            'name'            => $this->faker->name(),
            'email'           => $this->faker->safeEmail(),
            'phone' => $parsed['e164'],
            'numeric_code' => $parsed['numeric_code'],
            'iso_code' => $parsed['iso_code'],
            'budget' => $this->faker->numberBetween(0, 100),
            'pipeline' => $this->faker->randomElement(['paid social - leads', 'sales pipeline']),
            'status' => $this->faker->randomElement(['open', 'lost', 'won', 'abandoned']),
            'fields'          => [
                'message' => $this->faker->sentence(10) 
            ]
        ];
    }
}