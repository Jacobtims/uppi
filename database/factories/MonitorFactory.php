<?php

namespace Database\Factories;

use App\Enums\Checks\Status;
use App\Enums\Monitors\MonitorType;
use App\Models\Monitor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Symfony\Component\Uid\Ulid;

class MonitorFactory extends Factory
{
    protected $model = Monitor::class;

    public function definition(): array
    {
        return [
        
            'name' => $this->faker->words(3, true),
            'type' => $this->faker->randomElement(MonitorType::cases()),
            'address' => $this->faker->url,
            'port' => $this->faker->numberBetween(80, 9000),
            'interval' => $this->faker->randomElement([1, 5, 15, 30, 60]),
            'consecutive_threshold' => $this->faker->randomElement([1, 2, 3]),
            'is_enabled' => true,
            'status' => Status::UNKNOWN,
            'user_id' => User::factory(),
            'next_check_at' => now(),
        ];
    }

    public function http(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => MonitorType::HTTP,
                'address' => 'https://example.com',
                'port' => null,
            ];
        });
    }

    public function tcp(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => MonitorType::TCP,
                'address' => 'google.com',
                'port' => 80,
            ];
        });
    }

    public function disabled(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_enabled' => false,
            ];
        });
    }

    public function failed(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Status::FAIL,
            ];
        });
    }
}
