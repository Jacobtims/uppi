<?php

namespace Database\Factories;

use App\Enums\StatusPage\UpdateStatus;
use App\Enums\StatusPage\UpdateType;
use App\Models\User;
use App\Models\Update;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Update>
 */
class UpdateFactory extends Factory
{
    protected $model = Update::class;

    public function definition(): array
    {
        $from = now()->subDays(value: fake()->numberBetween(1, 30));
        $title = fake()->sentence();    
        $slug = now()->format('YmdHis') . '-' . str($title)->slug() . '-' . fake()->randomNumber(6, true);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => $slug,
            'content' => fake()->paragraph(),
            'from' => $from,
            'to' => $from->addDays(value: fake()->numberBetween(1, 30)),
            'is_published' => fake()->boolean(),
            'is_featured' => fake()->boolean(),
            'type' => fake()->randomElement(UpdateType::cases()),
            'status' => fake()->randomElement(UpdateStatus::cases()),
        ];
    }
}
