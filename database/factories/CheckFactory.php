<?php

namespace Database\Factories;

use App\Enums\Checks\Status;
use App\Models\Check;
use App\Models\Monitor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Symfony\Component\Uid\Ulid;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Check>
 */
class CheckFactory extends Factory
{
    protected $model = Check::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Ulid::generate(),
            'status' => Status::OK,
            'checked_at' => now(),
            'monitor_id' => Monitor::factory(),
        ];
    }
}
