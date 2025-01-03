<?php

namespace Database\Factories;

use App\Models\Monitor;
use App\Models\StatusPage;
use App\Models\StatusPageItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StatusPageItem>
 */
class StatusPageItemFactory extends Factory
{
    protected $model = StatusPageItem::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'status_page_id' => StatusPage::factory(),
            'monitor_id' => Monitor::factory(),
            'is_enabled' => true,
            'is_showing_favicon' => $this->faker->boolean,
        ];
    }
}
