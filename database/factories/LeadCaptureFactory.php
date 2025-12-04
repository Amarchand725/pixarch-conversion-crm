<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Status;
use App\Modules\Campaign\Models\Campaign;
use App\Modules\LeadCapture\Models\LeadCapture;
use App\Modules\LeadCapture\Models\CaptureFormField;

class LeadCaptureFactory extends Factory
{
    protected $model = LeadCapture::class;

    public function definition(): array
    {
        $statusId = Status::where('model', 'LeadCapture')
                          ->where('name', 'active')
                          ->value('id');

        return [
            'status_id'   => $statusId,
            'campaign_id' => $this->faker->randomElement(Campaign::pluck('id')->toArray()),
            'name'        => $this->faker->sentence(2),
            'description' => $this->faker->paragraph(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (LeadCapture $capture) {
            CaptureFormField::factory()
                ->count(5) // 🔥 create 5 fields per lead capture
                ->create([
                    'lead_capture_id' => $capture->id,
                ]);
        });
    }
}