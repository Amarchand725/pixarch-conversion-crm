<?php
namespace Database\Factories;

use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Lead\Models\Lead;
use App\Modules\LeadCapture\Models\LeadCapture;

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
        return [
            'uuid'            => $this->faker->uuid(),
            'source_id' => $this->faker->randomElement(Source::pluck('id')->toArray()),
            'lead_capture_id' => $this->faker->randomElement(LeadCapture::pluck('id')->toArray()),
            'name'            => $this->faker->name(),
            'email'           => $this->faker->safeEmail(),
            'phone'           => $this->faker->phoneNumber(),
            'value' => $this->faker->numberBetween(0, 100),
            'pipeline' => $this->faker->randomElement(['paid social - leads', 'sales pipeline']),
            'status' => $this->faker->randomElement(['open', 'lost', 'won', 'abandoned']),
            'fields'          => [
                'message' => $this->faker->sentence(10) 
            ]
        ];
    }
}