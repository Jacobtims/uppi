<?php

namespace App\Filament\Resources\StatusPageResource\Pages;

use App\Filament\Resources\StatusPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStatusPage extends CreateRecord
{
    protected static string $resource = StatusPageResource::class;

    protected ?string $subheading = 'Set up a status page to keep your users informed. Share your public link with your users, or embed the widget on your website..';
}
