<?php

namespace App\Filament\Widgets;

use App\Models\Anomaly;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActiveAnomalies extends BaseWidget
{

    protected int|string|array $columnSpan = 12;
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
                Tables\Columns\TextColumn::make('monitor.address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->searchable(false)
            ->emptyStateIcon('heroicon-o-face-smile')
            ->emptyStateHeading('No anomalies found')
            ->emptyStateDescription('All systems are running smoothly')
            ->paginated(false);
    }

}
