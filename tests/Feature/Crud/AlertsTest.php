<?php

use App\Enums\Types\AlertType;
use App\Filament\Resources\AlertResource;
use App\Models\Alert;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can view alerts page', function () {
    $this->get(AlertResource::getUrl())
        ->assertSuccessful();
});

test('can create an email alert through Uppi UI', function () {
    $alertData = [
        'data.name' => 'Test Email Alert',
        'data.type' => AlertType::EMAIL->value,
        'data.destination' => 'test@example.com',
        'data.is_enabled' => true,
    ];

    Livewire::test(AlertResource\Pages\ManageAlerts::class)
        ->callPageAction('create', $alertData);

    $this->assertDatabaseHas('alerts', [
        'name' => 'Test Email Alert',
        'type' => AlertType::EMAIL->value,
        'destination' => 'test@example.com',
        'is_enabled' => true,
        'user_id' => $this->user->id,
    ]);
});

test('can create a slack alert through Uppi UI', function () {
    $alertData = [
        'data.name' => 'Test Slack Alert',
        'data.type' => AlertType::SLACK->value,
        'data.destination' => '#monitoring',
        'data.is_enabled' => true,
        'data.config' => [
            'slack_token' => 'xoxb-test-token',
        ],
    ];

    Livewire::test(AlertResource\Pages\ManageAlerts::class)
        ->callPageAction('create', $alertData);

    $this->assertDatabaseHas('alerts', [
        'name' => 'Test Slack Alert',
        'type' => AlertType::SLACK->value,
        'destination' => '#monitoring',
        'is_enabled' => true,
        'user_id' => $this->user->id,
    ]);

    $alert = Alert::first();
    expect($alert->config)->toHaveKey('slack_token')
        ->and($alert->config['slack_token'])->toBe('xoxb-test-token');
});

test('can create a bird alert through Uppi UI', function () {
    $alertData = [
        'data.name' => 'Test Bird Alert',
        'data.type' => AlertType::BIRD->value,
        'data.destination' => '+31612345678',
        'data.is_enabled' => true,
        'data.config' => [
            'bird_api_key' => 'test-api-key',
            'bird_workspace_id' => 'test-workspace',
            'bird_channel_id' => 'test-channel',
        ],
    ];

    Livewire::test(AlertResource\Pages\ManageAlerts::class)
        ->callPageAction('create', $alertData);

    $this->assertDatabaseHas('alerts', [
        'name' => 'Test Bird Alert',
        'type' => AlertType::BIRD->value,
        'destination' => '+31612345678',
        'is_enabled' => true,
        'user_id' => $this->user->id,
    ]);

    $alert = Alert::first();
    expect($alert->config)
        ->toHaveKey('bird_api_key')
        ->toHaveKey('bird_workspace_id')
        ->toHaveKey('bird_channel_id');
});

test('can create a pushover alert through Uppi UI', function () {
    $alertData = [
        'data.name' => 'Test Pushover Alert',
        'data.type' => AlertType::PUSHOVER->value,
        'data.destination' => 'user-key',
        'data.is_enabled' => true,
        'data.config' => [
            'pushover_api_token' => 'app-token',
        ],
    ];

    Livewire::test(AlertResource\Pages\ManageAlerts::class)
        ->callPageAction('create', $alertData);

    $this->assertDatabaseHas('alerts', [
        'name' => 'Test Pushover Alert',
        'type' => AlertType::PUSHOVER->value,
        'destination' => 'user-key',
        'is_enabled' => true,
        'user_id' => $this->user->id,
    ]);

    $alert = Alert::first();
    expect($alert->config)->toHaveKey('pushover_api_token')
        ->and($alert->config['pushover_api_token'])->toBe('app-token');
});

test('can create an expo alert through Uppi UI', function () {
    $alertData = [
        'data.name' => 'Test Expo Alert',
        'data.type' => AlertType::EXPO->value,
        'data.destination' => 'ExponentPushToken[xxxxxxxxxxxxxxxxxxxxxx]',
        'data.is_enabled' => true,
    ];

    Livewire::test(AlertResource\Pages\ManageAlerts::class)
        ->callPageAction('create', $alertData);

    $this->assertDatabaseHas('alerts', [
        'name' => 'Test Expo Alert',
        'type' => AlertType::EXPO->value,
        'destination' => 'ExponentPushToken[xxxxxxxxxxxxxxxxxxxxxx]',
        'is_enabled' => true,
        'user_id' => $this->user->id,
    ]);
});

test('validates required fields when creating an alert', function () {
    Livewire::test(AlertResource\Pages\ManageAlerts::class)
        ->callPageAction('create', [
            'data.name' => '',
            'data.type' => AlertType::EMAIL->value,
            'data.destination' => '',
        ])
        ->assertHasPageActionErrors(['data.name', 'data.destination']);
});

test('validates email format for email alerts', function () {
    Livewire::test(AlertResource\Pages\ManageAlerts::class)
        ->callPageAction('create', [
            'data.name' => 'Test Alert',
            'data.type' => AlertType::EMAIL->value,
            'data.destination' => 'not-an-email',
        ])
        ->assertHasPageActionErrors(['data.destination']);
});

test('requires slack token for slack alerts', function () {
    Livewire::test(AlertResource\Pages\ManageAlerts::class)
        ->callPageAction('create', [
            'data.name' => 'Test Slack Alert',
            'data.type' => AlertType::SLACK->value,
            'data.destination' => '#monitoring',
            'data.config' => [],
        ])
        ->assertHasPageActionErrors(['data.config.slack_token']);
});

test('can enable and disable alerts', function () {
    $alert = Alert::factory()->email()->create([
        'user_id' => $this->user->id,
        'is_enabled' => true,
    ]);

    Livewire::test(AlertResource\Pages\ManageAlerts::class)
        ->callTableAction('disable', $alert);

    expect($alert->fresh()->is_enabled)->toBeFalse();

    Livewire::test(AlertResource\Pages\ManageAlerts::class)
        ->callTableAction('enable', $alert);

    expect($alert->fresh()->is_enabled)->toBeTrue();
});

test('can delete alerts', function () {
    $alert = Alert::factory()->email()->create([
        'user_id' => $this->user->id,
    ]);

    Livewire::test(AlertResource\Pages\ManageAlerts::class)
        ->callTableAction('delete', $alert);

    $this->assertModelMissing($alert);
});

test('can bulk enable and disable alerts', function () {
    $alerts = Alert::factory()->email()->count(3)->create([
        'user_id' => $this->user->id,
        'is_enabled' => false,
    ]);

    Livewire::test(AlertResource\Pages\ManageAlerts::class)
        ->callTableBulkAction('enable', $alerts);

    foreach ($alerts as $alert) {
        expect($alert->fresh()->is_enabled)->toBeTrue();
    }

    Livewire::test(AlertResource\Pages\ManageAlerts::class)
        ->callTableBulkAction('disable', $alerts);

    foreach ($alerts as $alert) {
        expect($alert->fresh()->is_enabled)->toBeFalse();
    }
}); 

it('cannot access someone else\'s alerts', function () {
    $alert = Alert::factory()->email()->create([
        'user_id' => User::factory()->create()->id,
    ]);

    $this->actingAs($this->user)
        ->get(AlertResource::getUrl('edit', ['record' => $alert]))
        ->assertForbidden();
});
