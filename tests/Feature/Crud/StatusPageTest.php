<?php

use App\Filament\Resources\StatusPageResource;
use App\Models\Monitor;
use App\Models\StatusPage;
use App\Models\StatusPageItem;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can view status pages list', function () {
    $this->get(StatusPageResource::getUrl())
        ->assertSuccessful();
});

test('can create a status page', function () {
    $pageData = [
        'data.name' => 'Test Status Page',
        'data.slug' => 'test-status-page',
        'data.is_enabled' => true,
        'data.website_url' => 'https://example.com',
    ];

    Livewire::test(StatusPageResource\Pages\CreateStatusPage::class)
        ->set($pageData)
        ->call('create')
        ->assertHasNoErrors();

    assertDatabaseHas('status_pages', [
        'name' => 'Test Status Page',
        'slug' => 'test-status-page',
        'is_enabled' => true,
        'website_url' => 'https://example.com',
        'user_id' => $this->user->id,
    ]);
});

test('can create a status page with logo', function () {
    $logo = UploadedFile::fake()->image('logo.png', 100, 100);

    $pageData = [
        'data.name' => 'Test Status Page',
        'data.slug' => 'test-status-page',
        'data.is_enabled' => true,
        'data.website_url' => 'https://example.com',
        'data.logo_url' => $logo,
    ];

    Livewire::test(StatusPageResource\Pages\CreateStatusPage::class)
        ->set($pageData)
        ->call('create')
        ->assertHasNoErrors();

    $statusPage = StatusPage::first();
    expect($statusPage->logo_url)->not->toBeNull();
    Storage::disk('public')->assertExists($statusPage->logo_url);
});

test('validates required fields', function () {
    Livewire::test(StatusPageResource\Pages\CreateStatusPage::class)
        ->set([
            'data.name' => '',
            'data.slug' => '',
        ])
        ->call('create')
        ->assertHasErrors(['data.name', 'data.slug']);
});

test('validates unique slug', function () {
    StatusPage::factory()->create([
        'slug' => 'test-page',
        'user_id' => $this->user->id,
    ]);

    Livewire::test(StatusPageResource\Pages\CreateStatusPage::class)
        ->set([
            'data.name' => 'Test Page',
            'data.slug' => 'test-page',
        ])
        ->call('create')
        ->assertHasErrors(['data.slug']);
});

test('validates website url format', function () {
    Livewire::test(StatusPageResource\Pages\CreateStatusPage::class)
        ->set([
            'data.name' => 'Test Page',
            'data.slug' => 'test-page',
            'data.website_url' => 'not-a-url',
        ])
        ->call('create')
        ->assertHasErrors(['data.website_url']);
});

test('validates logo file size and type', function () {
    $largeLogo = UploadedFile::fake()->image('large.png')->size(2000); // 2MB
    $invalidFile = UploadedFile::fake()->create('document.pdf', 100);

    Livewire::test(StatusPageResource\Pages\CreateStatusPage::class)
        ->set([
            'data.name' => 'Test Page',
            'data.slug' => 'test-page',
            'data.logo_url' => $largeLogo,
        ])
        ->call('create')
        ->assertHasErrors(['data.logo_url']);

    Livewire::test(StatusPageResource\Pages\CreateStatusPage::class)
        ->set([
            'data.name' => 'Test Page',
            'data.slug' => 'test-page',
            'data.logo_url' => $invalidFile,
        ])
        ->call('create')
        ->assertHasErrors(['data.logo_url']);
});

test('can add monitors to status page', function () {
    $statusPage = StatusPage::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $monitor = Monitor::factory()->create([
        'user_id' => $this->user->id,
    ]);

    Livewire::test(StatusPageResource\RelationManagers\ItemsRelationManager::class, [
        'ownerRecord' => $statusPage,
        'pageClass' => StatusPageResource\Pages\EditStatusPage::class,
    ])
    ->callTableAction('create', data: [
        'name' => 'Test Monitor',
        'monitor_id' => $monitor->id,
        'is_enabled' => true,
        'is_showing_favicon' => true,
    ]);

    assertDatabaseHas('status_page_items', [
        'name' => 'Test Monitor',
        'status_page_id' => $statusPage->id,
        'monitor_id' => $monitor->id,
        'is_enabled' => true,
        'is_showing_favicon' => true,
    ]);
});

test('can remove monitors from status page', function () {
    $statusPage = StatusPage::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $monitor = Monitor::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $item = StatusPageItem::factory()->create([
        'status_page_id' => $statusPage->id,
        'monitor_id' => $monitor->id,
    ]);

    Livewire::test(StatusPageResource\RelationManagers\ItemsRelationManager::class, [
        'ownerRecord' => $statusPage,
        'pageClass' => StatusPageResource\Pages\EditStatusPage::class,
    ])
    ->callTableAction('delete', $item);

    assertDatabaseMissing('status_page_items', [
        'id' => $item->id,
    ]);
});

test('can toggle monitor visibility on status page', function () {
    $statusPage = StatusPage::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $monitor = Monitor::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $item = StatusPageItem::factory()->create([
        'status_page_id' => $statusPage->id,
        'monitor_id' => $monitor->id,
        'is_enabled' => true,
    ]);

    Livewire::test(StatusPageResource\RelationManagers\ItemsRelationManager::class, [
        'ownerRecord' => $statusPage,
        'pageClass' => StatusPageResource\Pages\EditStatusPage::class,
    ])
    ->mountTableAction('edit', $item->id)
    ->setTableActionData([
        'is_enabled' => false,
    ])
    ->callMountedTableAction();

    expect($item->fresh()->is_enabled)->toBeFalse();
});

test('can toggle favicon visibility on status page', function () {
    $statusPage = StatusPage::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $monitor = Monitor::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $item = StatusPageItem::factory()->create([
        'status_page_id' => $statusPage->id,
        'monitor_id' => $monitor->id,
        'is_showing_favicon' => true,
    ]);

    Livewire::test(StatusPageResource\RelationManagers\ItemsRelationManager::class, [
        'ownerRecord' => $statusPage,
        'pageClass' => StatusPageResource\Pages\EditStatusPage::class,
    ])
    ->mountTableAction('edit', $item->id)
    ->setTableActionData([
        'is_showing_favicon' => false,
    ])
    ->callMountedTableAction();

    expect($item->fresh()->is_showing_favicon)->toBeFalse();
});