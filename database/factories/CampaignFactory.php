<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Status;
use App\Modules\Campaign\Models\Campaign;
use App\Modules\LeadCapture\Models\LeadCapture;

class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random active status id from statuses table
        $statusId = Status::where('model', 'Campaign')->where('name', 'active')->value('id');

        return [
            'status_id'   => $statusId,
            'name'        => $this->faker->sentence(3),
            'type'        => $this->faker->randomElement(['Email', 'Social', 'Call']),
            'budget'      => $this->faker->numberBetween(500, 50000),  // numeric budget
            'start_date'  => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_date'    => $this->faker->dateTimeBetween('now', '+1 month'),
            'description' => $this->faker->paragraph(),
        ];
    }
}
