<?php

namespace App\Filament\Resources;

use App\Enums\Monitors\MonitorType;
use App\Filament\Resources\MonitorResource\Pages;
use App\Filament\Resources\MonitorResource\RelationManagers\AlertsRelationManager;
use App\Models\Monitor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MonitorResource extends Resource
{
    protected static ?string $model = Monitor::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->options(MonitorType::class)
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->live()
                            ->url(fn (Get $get) => $get('type') === MonitorType::TCP->value ? null : 'https://'.$get('address')),
                        Forms\Components\TextInput::make('port')
                            ->numeric()
                            ->requiredIf('type', MonitorType::TCP->value)
                            ->hidden(fn (Get $get) => $get('type') !== MonitorType::TCP->value)
                            ->live(),
                        Forms\Components\Toggle::make('is_enabled')
                            ->required()
                            ->default(true)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Monitor Settings')
                    ->schema([
                        Forms\Components\TextInput::make('interval')
                            ->required()
                            ->numeric()
                            ->default(5)
                            ->step(1)
                            ->minValue(1)
                            ->helperText('Check interval in minutes'),
                        Forms\Components\TextInput::make('consecutive_threshold')
                            ->required()
                            ->numeric()
                            ->default(state: 2)
                            ->step(1)
                            ->minValue(1)
                            ->helperText('Number of failed checks in a row needed before registering an anomaly and sending an alert'),
                        Forms\Components\TextInput::make('user_agent')
                            ->placeholder(config('app.name'))
                            ->hidden(fn (Get $get) => $get('type') !== MonitorType::HTTP->value)
                            ->maxLength(255)
                            ->helperText('Custom User-Agent string for HTTP requests')
                            ->live(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->description(fn ($record) => ! $record->is_enabled ? 'Inactive' : null),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expects')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->boolean()
                    ->label('Enabled')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('interval')
                    ->numeric()
                    ->sortable()
                    ->suffix(' min')
                    ->description(description: fn ($record) => $record->consecutive_threshold.'x'),
                Tables\Columns\TextColumn::make('alerts.name')
                    ->size('xs')
                    ->label('Alerts')
                    ->wrap(),
                Tables\Columns\TextColumn::make('last_checked_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('enable')
                        ->label('Enable')
                        ->action(fn ($records) => $records->each->update(['is_enabled' => true]))
                        ->deselectRecordsAfterCompletion()
                        ->icon('heroicon-o-check'),
                    Tables\Actions\BulkAction::make('disable')
                        ->label('Disable')
                        ->action(fn ($records) => $records->each->update(['is_enabled' => false]))
                        ->deselectRecordsAfterCompletion()
                        ->icon('heroicon-o-x-mark'),
                    Tables\Actions\BulkAction::make('set_alerts')
                        ->label('Set Alerts')
                        ->form([
                            Forms\Components\Select::make('alerts')
                                ->translateLabel()
                                ->options(fn ($record) => auth()->user()->alerts->pluck('name', 'id'))
                                ->multiple()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function ($records, $data) {
                            $records->each(function ($record) use ($data) {
                                $record->alerts()->sync($data['alerts']);
                            });
                        })
                        ->icon('heroicon-o-bell'),
                    Tables\Actions\DeleteBulkAction::make(),

                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AlertsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonitors::route('/'),
            'create' => Pages\CreateMonitor::route('/create'),
            'edit' => Pages\EditMonitor::route('/{record}/edit'),
        ];
    }
}
