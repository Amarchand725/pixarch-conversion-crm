<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Status;
use App\Modules\Campaign\Models\Campaign;
use App\Models\User;

class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition(): array
    {
        $statusId = Status::where('model', 'Campaign')
            ->where('name', 'active')
            ->value('id');

        return [
            'status_id'   => $statusId,
            'name'        => $this->faker->sentence(3),
            'type'        => $this->faker->randomElement(['Email', 'Social', 'Call']),
            'budget'      => $this->faker->numberBetween(500, 50000),
            'start_date'  => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_date'    => $this->faker->dateTimeBetween('now', '+1 month'),
            'description' => $this->faker->paragraph(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Campaign $campaign) {
            // Get random agents (auto_assigned type, excluding Admin)
            $agents = User::where('type', 'auto_assigned')
                ->whereHas('roles', fn($q) => $q->where('name', '!=', 'Admin'))
                ->inRandomOrder()
                ->take(rand(1, 3)) // assign 1-3 random agents
                ->pluck('id');

            $campaign->agents()->attach($agents);
        });
    }
}