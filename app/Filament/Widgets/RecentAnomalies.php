<?php

namespace App\Filament\Widgets;

use App\Models\Anomaly;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAnomalies extends BaseWidget
{

    protected int|string|array $columnSpan = 4;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Anomaly::query()
                    ->where('ended_at', null)
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('monitor.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alert.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->searchable(false);
    }

}
