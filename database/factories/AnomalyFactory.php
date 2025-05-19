<?php

namespace Database\Factories;

use App\Models\Monitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Anomaly>
 */
class AnomalyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'monitor_id' => Monitor::factory(),
            'started_at' => now(),
            'ended_at' => null,
        ];
    }

    /**
     * Indicate that the anomaly has been resolved.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'ended_at' => now(),
        ]);
    }

    /**
     * Indicate that the anomaly started at a specific time ago.
     */
    public function startedAgo(string $duration): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => now()->sub($duration),
        ]);
    }

    /**
     * Indicate that the anomaly was resolved after a specific duration.
     */
    public function resolvedAfter(string $duration): static
    {
        return $this->state(fn (array $attributes) => [
            'ended_at' => now()->add($duration),
        ]);
    }

    /**
     * Create an anomaly that has been active for a specific duration.
     */
    public function lasting(string $duration): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => now()->sub($duration),
            'ended_at' => now(),
        ]);
    }
}
