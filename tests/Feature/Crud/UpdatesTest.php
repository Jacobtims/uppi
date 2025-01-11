<?php

use App\Enums\StatusPage\UpdateStatus;
use App\Enums\StatusPage\UpdateType;
use App\Filament\Resources\UpdateResource;
use App\Models\Monitor;
use App\Models\StatusPage;
use App\Models\Update;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can view updates list', function () {
    $this->get(UpdateResource::getUrl())
        ->assertSuccessful();
});

test('can create an update', function () {
    $updateData = [
        'data.title' => 'Test Update',
        'data.content' => 'This is a test update',
        'data.type' => UpdateType::UPDATE->value,
        'data.status' => UpdateStatus::NEW->value,
        'data.is_published' => true,
        'data.slug' => 'test-update',
    ];

    Livewire::test(UpdateResource\Pages\CreateUpdate::class)
        ->set($updateData)
        ->call('create')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('updates', [
        'title' => 'Test Update',
        'content' => 'This is a test update',
        'type' => UpdateType::UPDATE->value,
        'status' => UpdateStatus::NEW->value,
        'is_published' => true,
        'slug' => 'test-update',
        'user_id' => $this->user->id,
    ]);
});

test('can create an update with image', function () {
    $image = UploadedFile::fake()->image('update.jpg');

    $updateData = [
        'data.title' => 'Test Update with Image',
        'data.content' => 'This is a test update with image',
        'data.type' => UpdateType::UPDATE->value,
        'data.status' => UpdateStatus::NEW->value,
        'data.is_published' => true,
        'data.slug' => 'test-update-with-image',
        'data.image' => $image,
    ];

    Livewire::test(UpdateResource\Pages\CreateUpdate::class)
        ->set($updateData)
        ->call('create')
        ->assertHasNoErrors();

    $update = Update::first();
    expect($update->image)->not->toBeNull();
    Storage::disk('public')->assertExists($update->image);
});

test('can create an update with monitors', function () {
    $monitor = Monitor::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $updateData = [
        'data.title' => 'Test Update with Monitor',
        'data.content' => 'This is a test update with monitor',
        'data.type' => UpdateType::ANOMALY->value,
        'data.status' => UpdateStatus::NEW->value,
        'data.is_published' => true,
        'data.slug' => 'test-update-with-monitor',
        'data.monitors' => [$monitor->id],
    ];

    Livewire::test(UpdateResource\Pages\CreateUpdate::class)
        ->set($updateData)
        ->call('create')
        ->assertHasNoErrors();

    $update = Update::first();
    expect($update->monitors)->toHaveCount(1)
        ->and($update->monitors->first()->id)->toBe($monitor->id);
});

test('can create an update with status pages', function () {
    $statusPage = StatusPage::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $updateData = [
        'data.title' => 'Test Update with Status Page',
        'data.content' => 'This is a test update with status page',
        'data.type' => UpdateType::UPDATE->value,
        'data.status' => UpdateStatus::NEW->value,
        'data.is_published' => true,
        'data.slug' => 'test-update-with-status-page',
        'data.status_pages' => [$statusPage->id],
    ];

    Livewire::test(UpdateResource\Pages\CreateUpdate::class)
        ->set($updateData)
        ->call('create')
        ->assertHasNoErrors();

    $update = Update::first();
    expect($update->statusPages)->toHaveCount(1)
        ->and($update->statusPages->first()->id)->toBe($statusPage->id);
});

test('validates required fields', function () {
    Livewire::test(UpdateResource\Pages\CreateUpdate::class)
        ->set([
            'data.title' => '',
            'data.content' => '',
            'data.type' => '',
            'data.status' => '',
            'data.slug' => '',
        ])
        ->call('create')
        ->assertHasErrors(['data.title', 'data.content', 'data.type', 'data.status'])
        ->assertHasNoErrors(['data.slug']);
});

test('validates unique slug', function () {
    Update::factory()->create([
        'slug' => 'test-update',
        'user_id' => $this->user->id,
    ]);

    Livewire::test(UpdateResource\Pages\CreateUpdate::class)
        ->set([
            'data.title' => 'Test Update',
            'data.content' => 'Content',
            'data.type' => UpdateType::UPDATE->value,
            'data.status' => UpdateStatus::NEW->value,
            'data.slug' => 'test-update',
        ])
        ->call('create')
        ->assertHasErrors(['data.slug']);
});

test('can edit an update', function () {
    $update = Update::factory()->create([
        'user_id' => $this->user->id,
    ]);

    Livewire::test(UpdateResource\Pages\EditUpdate::class, [
        'record' => $update->getKey(),
    ])
    ->set([
        'data.title' => 'Updated Title',
        'data.content' => 'Updated content',
    ])
    ->call('save')
    ->assertHasNoErrors();

    $this->assertDatabaseHas('updates', [
        'id' => $update->id,
        'title' => 'Updated Title',
        'content' => 'Updated content',
    ]);
});

test('can toggle update featured status', function () {
    $update = Update::factory()->create([
        'user_id' => $this->user->id,
        'is_featured' => false,
    ]);

    Livewire::test(UpdateResource\Pages\EditUpdate::class, [
        'record' => $update->getKey(),
    ])
    ->set('data.is_featured', true)
    ->call('save')
    ->assertHasNoErrors();

    expect($update->fresh()->is_featured)->toBeTrue();
});

test('can set update date range', function () {
    $from = now();
    $to = now()->addDay();

    Livewire::test(UpdateResource\Pages\CreateUpdate::class)
        ->set([
            'data.title' => 'Test Update',
            'data.content' => 'Content',
            'data.type' => UpdateType::MAINTENANCE->value,
            'data.status' => UpdateStatus::NEW->value,
            'data.slug' => 'test-update',
            'data.from' => $from,
            'data.to' => $to,
        ])
        ->call('create')
        ->assertHasNoErrors();

    $update = Update::first();
    expect($update->from->timestamp)->toBe($from->timestamp)
        ->and($update->to->timestamp)->toBe($to->timestamp);
});

test('cannot access updates from other users', function () {
    $otherUser = User::factory()->create();
    $update = Update::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $this->get(UpdateResource::getUrl('edit', [
        'record' => $update->getKey(),
    ]))
    ->assertNotFound();
}); 