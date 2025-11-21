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

        // Get a random lead_capture id
        $leadCaptureId = LeadCapture::inRandomOrder()->value('id');

        return [
            'status_id'        => $statusId,
            'lead_capture_id'  => $leadCaptureId,
            'name'             => $this->faker->sentence(3),
            'description'      => $this->faker->paragraph(),
        ];
    }
}
