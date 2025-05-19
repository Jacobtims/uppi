<?php

namespace App\Filament\Resources\UpdateResource\Pages;

use App\Filament\Resources\UpdateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUpdate extends CreateRecord
{
    protected static string $resource = UpdateResource::class;

    protected ?string $subheading = 'Write an update to share on your status pages. Announce scheduled maintenance, important changes, or share updates on incidents real-time.';
}
