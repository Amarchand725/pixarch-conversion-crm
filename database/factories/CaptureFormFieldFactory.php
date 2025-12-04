<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\LeadCapture\Models\LeadCapture;
use App\Modules\LeadCapture\Models\CaptureFormField;

class CaptureFormFieldFactory extends Factory
{
    protected $model = CaptureFormField::class;

    public function definition()
    {
        return [
            'lead_capture_id' => LeadCapture::factory(), // links to unique lead capture
            'label' => $this->faker->word(),
            'name' => $this->faker->slug(),
            'type' => $this->faker->randomElement(['text', 'email', 'tel', 'textarea']),
            'placeholder' => $this->faker->sentence(2),
            'required' => $this->faker->boolean(60),
            'options' => null,
            'order' => 0,
        ];
    }
}
