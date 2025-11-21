<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Lead\Models\Lead;

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
            'lead_capture_id' => $this->faker->numberBetween(1, 5),
            'name'            => $this->faker->name(),
            'email'           => $this->faker->safeEmail(),
            'phone'           => $this->faker->phoneNumber(),
            'value' => $this->faker->numberBetween(0, 100),
            'fields'          => [
                'message' => $this->faker->sentence(10) 
            ]
        ];
    }
}