<?php

namespace Database\Factories;

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

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'from' => $from,
            'to' => $from->addDays(value: fake()->numberBetween(1, 30)),
            'is_published' => fake()->boolean(),
            'is_featured' => fake()->boolean(),
            'type' => fake()->randomElement(UpdateType::cases()),
        ];
    }
}
