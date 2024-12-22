<?php

namespace App\Filament\Resources;

use App\Enums\Types\AlertType;
use App\Filament\Resources\AlertResource\Pages;
use App\Models\Alert;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

final class AlertResource extends Resource
{
    protected static ?string $model = Alert::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationLabel = 'Destinations';

    protected static ?int $navigationSort = 3;

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

                Forms\Components\Section::make([
                    Forms\Components\Hidden::make('uppi_app_info')
                        ->required(),
                    Forms\Components\View::make('filament.forms.components.uppi-app-info')
                        ->viewData([
                            'personal_access_tokens_url' => PersonalAccessTokenResource::getUrl(),
                        ]),
                ])
                    ->columnSpanFull()
                    ->visible(fn(Get $get) => AlertType::tryFrom($get('type')) === AlertType::EXPO),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->hidden(fn(Get $get) => AlertType::tryFrom($get('type')) === AlertType::EXPO)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('destination')
                    ->helperText(function (Get $get) {
                        return match (AlertType::tryFrom($get('type'))) {
                            AlertType::EMAIL => 'The email address to send the alert to.',
                            AlertType::SLACK => 'The Slack channel to send the alert to.',
                            AlertType::BIRD => 'The phone number to send the alert to.',
                            AlertType::MESSAGEBIRD => 'The phone number to send the alert to.',
                            AlertType::PUSHOVER => 'Your PushOver User Key. Failure alerts will be sent with a emergency priority every 60 seconds for 3 minutes. Recovery alerts will be sent with a high priority.',
                            default => null,
                        };
                    })
                    ->prefix(fn(Get $get) => AlertType::tryFrom($get('type')) === AlertType::SLACK ? '#' : null)
                    ->password(fn(Get $get) => AlertType::tryFrom($get('type')) === AlertType::PUSHOVER)
                    ->live()
                    ->columnSpanFull()
                    ->hidden(fn(Get $get) => AlertType::tryFrom($get('type')) === AlertType::EXPO)
                    ->visible(fn(Get $get) => !empty($get('type')))
                    ->email(fn(Get $get) => AlertType::tryFrom($get('type')) === AlertType::EMAIL)
                    ->required(),

                Forms\Components\Toggle::make('is_enabled')
                    ->required()
                    ->default(true)
                    ->hidden(fn(Get $get) => AlertType::tryFrom($get('type')) === AlertType::EXPO)
                    ->columnSpanFull(),

                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('config.slack_token')
                        ->label('Slack Bot OAuth Token')
                        ->required(),
                ])
                    ->columnSpanFull()
                    ->live()
                    ->visible(fn(Get $get) => AlertType::tryFrom($get('type')) === AlertType::SLACK),

                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('config.bird_api_key')
                        ->required()
                        ->password()
                        ->label('API Key')
                        ->helperText('The API key for the Bird API.'),
                    Forms\Components\TextInput::make('config.bird_workspace_id')
                        ->label('Workspace ID')
                        ->helperText('The ID of the workspace that will be used to send the alert from.')
                        ->required(),
                    Forms\Components\TextInput::make('config.bird_channel_id')
                        ->label('Channel ID')
                        ->helperText('The ID of the channel that will be used to send the alert to.')
                        ->required(),
                ])
                    ->columnSpanFull()
                    ->live()
                    ->visible(fn(Get $get) => AlertType::tryFrom($get('type')) === AlertType::BIRD),

                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('config.pushover_api_token')
                        ->required()
                        ->password()
                        ->label('Application API Token')
                        ->helperText('The Application API Token for the PushOver API.')
                        ->hintAction(
                            \Filament\Forms\Components\Actions\Action::make('generate')
                                ->label('Create a new application')
                                ->icon('heroicon-m-arrow-top-right-on-square')
                                ->url('https://pushover.net/apps/build')
                                ->openUrlInNewTab()
                        ),
                ])
                    ->columnSpanFull()
                    ->live()
                    ->visible(fn(Get $get) => AlertType::tryFrom($get('type')) === AlertType::PUSHOVER),

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
                    ->visible(fn(Get $get) => AlertType::tryFrom($get('type')) === AlertType::MESSAGEBIRD),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('destination')
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        if (in_array($record->type, [AlertType::PUSHOVER, AlertType::EXPO])) {
                            return '************';
                        }

                        return $state;
                    }),
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
                Tables\Actions\EditAction::make()
                    ->hidden(fn(Alert $record) => $record->type === AlertType::EXPO),
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
