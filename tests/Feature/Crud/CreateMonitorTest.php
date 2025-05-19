<?php

use App\Enums\Monitors\MonitorType;
use App\Filament\Resources\MonitorResource;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can view monitor creation page', function () {
    $this->get(MonitorResource::getUrl('create'))
        ->assertSuccessful();
});

test('can create a monitor through Uppi UI', function () {
    $monitorData = [
        'data.name' => 'Test Website Monitor',
        'data.type' => MonitorType::HTTP->value,
        'data.address' => 'https://example.com',
        'data.interval' => 5,
        'data.consecutive_threshold' => 2,
        'data.is_enabled' => true,
    ];

    Livewire::test(MonitorResource\Pages\CreateMonitor::class)
        ->set($monitorData)
        ->call('create')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('monitors', [
        'name' => 'Test Website Monitor',
        'type' => MonitorType::HTTP->value,
        'address' => 'https://example.com',
        'interval' => 5,
        'consecutive_threshold' => 2,
        'is_enabled' => true,
        'user_id' => $this->user->id,
    ]);
});

test('can create a tcp monitor through Uppi UI', function () {
    $monitorData = [
        'data.name' => 'Test TCP Monitor',
        'data.type' => MonitorType::TCP->value,
        'data.address' => 'smtp.gmail.com',
        'data.port' => 587,
        'data.interval' => 5,
        'data.consecutive_threshold' => 2,
        'data.is_enabled' => true,
    ];

    Livewire::test(MonitorResource\Pages\CreateMonitor::class)
        ->set($monitorData)
        ->call('create')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('monitors', [
        'name' => 'Test TCP Monitor',
        'type' => MonitorType::TCP->value,
        'address' => 'smtp.gmail.com',
        'port' => 587,
        'interval' => 5,
        'consecutive_threshold' => 2,
        'is_enabled' => true,
        'user_id' => $this->user->id,
    ]);
});

test('validates required fields when creating a monitor', function () {
    Livewire::test(MonitorResource\Pages\CreateMonitor::class)
        ->set([
            'data.name' => '',
            'data.type' => MonitorType::HTTP->value,
            'data.address' => '',
        ])
        ->call('create')
        ->assertHasErrors(['data.name', 'data.address']);
});

test('requires port for tcp monitors', function () {
    Livewire::test(MonitorResource\Pages\CreateMonitor::class)
        ->set([
            'data.name' => 'TCP Monitor',
            'data.type' => MonitorType::TCP->value,
            'data.address' => 'smtp.gmail.com',
            'data.interval' => 5,
            'data.consecutive_threshold' => 2,
        ])
        ->call('create')
        ->assertHasErrors(['data.port']);
});

test('shows correct fields based on monitor type', function () {
    $component = Livewire::test(MonitorResource\Pages\CreateMonitor::class);

    // Initially, port should be hidden for HTTP monitors
    $component->set('data.type', MonitorType::HTTP->value)
        ->assertSet('data.port', null);

    // Port should be required for TCP monitors
    $component->set('data.type', MonitorType::TCP->value)
        ->set([
            'data.name' => 'TCP Monitor',
            'data.address' => 'smtp.gmail.com',
        ])
        ->call('create')
        ->assertHasErrors(['data.port']);
});

test('can create a monitor with custom user agent', function () {
    $monitorData = [
        'data.name' => 'Test Website Monitor',
        'data.type' => MonitorType::HTTP->value,
        'data.address' => 'https://example.com',
        'data.interval' => 5,
        'data.consecutive_threshold' => 2,
        'data.is_enabled' => true,
        'data.user_agent' => 'CustomBot/1.0',
    ];

    Livewire::test(MonitorResource\Pages\CreateMonitor::class)
        ->set($monitorData)
        ->call('create')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('monitors', [
        'name' => 'Test Website Monitor',
        'type' => MonitorType::HTTP->value,
        'address' => 'https://example.com',
        'user_agent' => 'CustomBot/1.0',
        'user_id' => $this->user->id,
    ]);
});

test('enforces minimum values for interval and threshold', function () {
    Livewire::test(MonitorResource\Pages\CreateMonitor::class)
        ->set([
            'data.name' => 'Test Monitor',
            'data.type' => MonitorType::HTTP->value,
            'data.address' => 'https://example.com',
            'data.interval' => 0,
            'data.consecutive_threshold' => 0,
        ])
        ->call('create')
        ->assertHasErrors(['data.interval', 'data.consecutive_threshold']);
});
