<?php

namespace Database\Factories;

use App\Models\StatusPage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StatusPage>
 */
class StatusPageFactory extends Factory
{
    protected $model = StatusPage::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'is_enabled' => true,
            'user_id' => User::factory(),
            'slug' => $this->faker->slug,
            'website_url' => $this->faker->url,
            'logo_url' => $this->faker->imageUrl,
        ];
    }
}
