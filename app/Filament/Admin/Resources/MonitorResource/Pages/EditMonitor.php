<?php

namespace App\Filament\Admin\Resources\MonitorResource\Pages;

use App\Filament\Admin\Resources\MonitorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMonitor extends EditRecord
{
    protected static string $resource = MonitorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
