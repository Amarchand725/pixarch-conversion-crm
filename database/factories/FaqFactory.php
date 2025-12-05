<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Status;
use App\Modules\Faq\Models\Faq;

class FaqFactory extends Factory
{
    protected $model = Faq::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random active status id from statuses table
        $statusId = Status::where('model', 'Faq')->where('name', 'active')->value('id');

        return [
            'status_id'   => $statusId,
            'question'        => $this->faker->sentence(5),
            'answer'        => $this->faker->paragraph(1),
            'order'    => $this->faker->numberBetween(1, 5),
        ];
    }
}
