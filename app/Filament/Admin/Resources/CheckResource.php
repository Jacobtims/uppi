<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CheckResource\Pages;
use App\Filament\Admin\Resources\CheckResource\RelationManagers;
use App\Models\Check;
use App\Traits\WithoutUserScopes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CheckResource extends Resource
{
    use WithoutUserScopes;

    protected static ?string $model = Check::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('monitor_id')
                    ->relationship('monitor', 'name')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('unknown'),
                Forms\Components\TextInput::make('response_time')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('response_code')
                    ->numeric()
                    ->default(null),
                Forms\Components\Textarea::make('output')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('checked_at')
                    ->required(),
                Forms\Components\Select::make('anomaly_id')
                    ->relationship('anomaly', 'id'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('monitor.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('response_time')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('response_code')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('checked_at')
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
                Tables\Columns\TextColumn::make('anomaly.id')
                    ->searchable(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChecks::route('/'),
            'create' => Pages\CreateCheck::route('/create'),
            'edit' => Pages\EditCheck::route('/{record}/edit'),
        ];
    }
}
