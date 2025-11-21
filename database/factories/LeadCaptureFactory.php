<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Status;
use App\Modules\LeadCapture\Models\LeadCapture;

class LeadCaptureFactory extends Factory
{
    protected $model = LeadCapture::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random active status id for LeadCapture model
        $statusId = Status::where('model', 'LeadCapture')
                          ->where('name', 'active')
                          ->value('id');

        // Generate fake form fields
        $fields = [
            [
                'label' => 'First Name',
                'type'  => 'text',
                'required' => true,
            ],
            [
                'label' => 'Last Name',
                'type'  => 'text',
                'required' => true,
            ],
            [
                'label' => 'Email',
                'type'  => 'email',
                'required' => true,
            ],
            [
                'label' => 'Phone',
                'type'  => 'tel',
                'required' => false,
            ],
            [
                'label' => 'Message',
                'type'  => 'textarea',
                'required' => false,
            ],
        ];

        return [
            'status_id' => $statusId,
            'name'      => $this->faker->sentence(3),
            'fields'    => json_encode($fields), // Store as JSON in DB
        ];
    }
}