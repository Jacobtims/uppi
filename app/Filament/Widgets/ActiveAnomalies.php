<?php

namespace App\Filament\Widgets;

use App\Models\Anomaly;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\View\View;

class ActiveAnomalies extends BaseWidget
{
    protected static ?string $heading = '';

    protected int|string|array $columnSpan = [
        'sm' => 12,
        'md' => 12,
        'lg' => 12,
    ];

    public function placeholder(): View
    {
        return view('filament.widgets.placeholder');
    }

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
                Tables\Columns\TextColumn::make('monitor.status')
                    ->badge()
                    ->searchable()
                    ->label(''),
                Tables\Columns\TextColumn::make('monitor.type')
                    ->searchable()
                    ->label(''),
                Tables\Columns\TextColumn::make('monitor.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('monitor.address')
                    ->label('Address')
                    ->searchable()
                    ->description(fn ($record) => $record->monitor->port),
                Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->state(function ($record) {
                        return $record->started_at->diffForHumans();
                    }),
            ])
            ->searchable(false)
            ->emptyStateIcon('heroicon-o-face-smile')
            ->emptyStateHeading('No anomalies found')
            ->emptyStateDescription('All systems are running smoothly')
            ->paginated(false)
            ->defaultSort('started_at', 'desc');
    }
}
