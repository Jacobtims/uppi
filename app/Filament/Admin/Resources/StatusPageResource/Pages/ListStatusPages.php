<?php

namespace App\Filament\Admin\Resources\StatusPageResource\Pages;

use App\Filament\Admin\Resources\StatusPageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStatusPages extends ListRecords
{
    protected static string $resource = StatusPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
