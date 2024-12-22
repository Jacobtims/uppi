<?php

namespace App\Filament\Admin\Resources\AnomalyResource\Pages;

use App\Filament\Admin\Resources\AnomalyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnomalies extends ListRecords
{
    protected static string $resource = AnomalyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
