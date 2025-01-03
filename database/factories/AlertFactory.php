<?php

namespace Database\Factories;

use App\Enums\Types\AlertType;
use App\Models\Alert;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Symfony\Component\Uid\Ulid;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alert>
 */
class AlertFactory extends Factory
{

    protected $model = Alert::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Ulid::generate(),
            'type' => $this->faker->randomElement(AlertType::cases()),
            'name' => $this->faker->company,
            'is_enabled' => true,
            'config' => [
                'id' => $this->faker->uuid,
            ],
            'user_id' => User::factory(),
        ];
    }
}
