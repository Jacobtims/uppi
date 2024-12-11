<?php

namespace App\Filament\Resources;

use App\Enums\Types\AlertType;
use App\Filament\Resources\AlertResource\Pages;
use App\Filament\Resources\AlertResource\RelationManagers;
use App\Models\Alert;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AlertResource extends Resource
{
    protected static ?string $model = Alert::class;

    protected static ?string $navigationGroup = 'Monitors';

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\ToggleButtons::make('type')
                ->options(AlertType::class)
                ->required()
                ->inline()
                ->live()
                ->columnSpanFull(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('destination')
                    ->helperText(function (Get $get) {
                        return match (AlertType::tryFrom($get('type'))) {
                            AlertType::EMAIL => 'The email address to send the alert to.',
                            AlertType::SLACK => 'The Slack channel to send the alert to.',
                            AlertType::BIRD => 'The phone number to send the alert to.',
                            default => null,
                        };
                    })
                    ->prefix(fn (Get $get) => AlertType::tryFrom($get('type')) === AlertType::SLACK ? '#' : null)
                    ->live()
                    ->visible(fn (Get $get) => !empty($get('type')))
                    ->email(fn (Get $get) => AlertType::tryFrom($get('type')) === AlertType::EMAIL)
                    ->required(),
                Forms\Components\Toggle::make('is_enabled')
                    ->required()
                    ->default(true)
                    ->columnSpanFull(),
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('config.slack_token')
                        ->label('Slack Bot OAuth Token')
                        ->required(),
                ])
                ->columnSpanFull()
                ->live()
                ->visible(fn (Get $get) => AlertType::tryFrom($get('type')) === AlertType::SLACK),

                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('config.bird_api_key')
                        ->required()
                        ->password()
                        ->label('API Key')
                        ->helperText('The API key for the MessageBird API.'),
                    Forms\Components\TextInput::make('config.bird_originator')
                        ->label('Originator')
                        ->helperText('The originator of the message. This is the name that will be displayed on the recipient\'s phone.')
                        ->required(),
                ])
                ->columnSpanFull()
                ->live()
                ->visible(fn (Get $get) => AlertType::tryFrom($get('type')) === AlertType::BIRD),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('destination')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->boolean(),
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAlerts::route('/'),
        ];
    }
}
