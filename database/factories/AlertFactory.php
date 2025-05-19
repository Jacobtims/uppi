<?php

namespace Database\Factories;

use App\Enums\Alerts\AlertType;
use App\Models\Alert;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlertFactory extends Factory
{
    protected $model = Alert::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'type' => $this->faker->randomElement(AlertType::cases()),
            'destination' => $this->faker->email,
            'is_enabled' => true,
            'user_id' => User::factory(),
        ];
    }

    public function email(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => AlertType::EMAIL,
                'destination' => $this->faker->email,
            ];
        });
    }

    public function slack(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => AlertType::SLACK,
                'destination' => '#'.$this->faker->word,
                'config' => [
                    'slack_token' => 'xoxb-'.$this->faker->uuid,
                ],
            ];
        });
    }

    public function bird(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => AlertType::BIRD,
                'destination' => '+'.$this->faker->numerify('##############'),
                'config' => [
                    'bird_api_key' => $this->faker->uuid,
                    'bird_workspace_id' => $this->faker->uuid,
                    'bird_channel_id' => $this->faker->uuid,
                ],
            ];
        });
    }

    public function pushover(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => AlertType::PUSHOVER,
                'destination' => $this->faker->uuid,
                'config' => [
                    'pushover_api_token' => $this->faker->uuid,
                ],
            ];
        });
    }

    public function expo(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => AlertType::EXPO,
                'destination' => 'ExponentPushToken['.$this->faker->regexify('[A-Za-z0-9]{24}').']',
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
}
