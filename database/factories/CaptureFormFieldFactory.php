<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\LeadCapture\Models\LeadCapture;
use App\Modules\LeadCapture\Models\CaptureFormField;
use Illuminate\Support\Str;

class CaptureFormFieldFactory extends Factory
{
    protected $model = CaptureFormField::class;

    public function definition()
    {
        $label = $this->faker->words(2, true); // more readable label
        $type = $this->faker->randomElement(['text', 'email', 'number', 'tel', 'textarea', 'select', 'file']);

        // Generate options if type is select
        $options = null;
        if ($type === 'select') {
            $optionCount = $this->faker->numberBetween(2, 5); // 2-5 options
            $optionsArray = $this->faker->words($optionCount);
            $options = implode(', ', $optionsArray); // comma-separated string
        }

        return [
            'lead_capture_id' => $this->faker->randomElement(LeadCapture::pluck('id')->toArray()), // links to unique lead capture
            'label' => $label,
            'name' => Str::snake($label),
            'type' => $type,
            'placeholder' => 'Enter '.$label,
            'required' => $this->faker->boolean(60),
            'options' => $options,
            'order' => 0,
        ];
    }
}
