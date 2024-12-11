<?php

namespace App\Filament\Resources;

use App\Enums\Checks\Status;
use App\Enums\Monitors\MonitorType;
use App\Filament\Resources\MonitorResource\Pages;
use App\Filament\Resources\MonitorResource\RelationManagers;
use App\Filament\Resources\MonitorResource\RelationManagers\AlertsRelationManager;
use App\Models\Monitor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MonitorResource extends Resource
{
    protected static ?string $model = Monitor::class;

    protected static ?string $navigationGroup = 'Monitors';

    protected static ?int $navigationSort = -999;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                            ->url(fn (Get $get) => $get('type') === MonitorType::TCP->value ? null : 'https://' . $get('address')),
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
                            ->default(1)
                            ->helperText('Check interval in minutes'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expects')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->boolean(),
                Tables\Columns\TextColumn::make('interval')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('last_checked_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
