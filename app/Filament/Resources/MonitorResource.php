<?php

namespace App\Filament\Resources;

use App\Enums\Monitors\MonitorType;
use App\Filament\Resources\MonitorResource\Pages;
use App\Filament\Resources\MonitorResource\RelationManagers\AlertsRelationManager;
use App\Models\Monitor;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class MonitorResource extends Resource
{
    protected static ?string $model = Monitor::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-heart';


    public static function getNavigationBadge(): ?string
    {
        if (! auth()->check()) {
            return null;
        }
        
        $count = auth()->user()->failingCount();

        if ($count === 0) {
            return null;
        }

        return $count . ' failing ' . \Str::plural('monitor', $count);
    }


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
                        Forms\Components\ToggleButtons::make('type')
                            ->inline()
                            ->grouped()
                            ->enum(MonitorType::class)
                            ->default(MonitorType::HTTP->value)
                            ->options(MonitorType::options())
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('address')
                            ->required(fn (Get $get) => $get('type') !== MonitorType::PULSE->value)
                            ->live()
                            ->url(fn (Get $get) => $get('type') === MonitorType::TCP->value ? null : 'https://'.$get('address'))
                            ->hidden(fn (Get $get) => $get('type') === MonitorType::PULSE->value),
                        Forms\Components\TextInput::make('port')
                            ->numeric()
                            ->requiredIf('type', MonitorType::TCP->value)
                            ->hidden(fn (Get $get) => $get('type') !== MonitorType::TCP->value)
                            ->live(),
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('pulse_url')
                                ->label('Pulse Check-in URL')
                                ->disabled()
                                ->readOnly()
                                ->prefixIcon('heroicon-s-globe-alt')
                                ->dehydrated(false)
                                ->placeholder('URL will be generated after saving')
                                ->helperText('This URL should be added to your cron job to check in with the server. If the endpoint doesn\'t get called within the interval, the monitor will be marked as down until it gets called again.')
                                ->formatStateUsing(fn (?Monitor $record) => $record ? url('/api/pulse/' . $record->id) . '?token=' . $record->address : null)
                                ->suffixAction(
                                    Forms\Components\Actions\Action::make('copy_url')
                                        ->label('Copy URL')
                                        ->icon('heroicon-o-clipboard')
                                        ->action(fn () => null)
                                        ->extraAttributes(fn ($state) => [
                                            'data-copy' => $state,
                                            'onclick' => 'navigator.clipboard.writeText(this.dataset.copy);',
                                        ])
                                )
                            ,
                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('generate_token')
                                    ->label('Generate New Token')
                                    ->action(function (Monitor $record) {
                                        $token = $record->generatePulseToken();
                                        $fullUrl = url('/api/pulse/' . $record->id) . '?token=' . $token;
                                    })
                                    ->color('warning')
                                    ->requiresConfirmation()
                                    ->modalDescription('This will generate a new token. Any existing token will be invalidated and old URLs will stop working.')
                                    ->modalSubmitActionLabel('Generate')
                                    ->visible(fn (?Monitor $record) => $record && $record->type === MonitorType::PULSE)
                                    ->icon('heroicon-o-key'),
                               
                            ])
                                ->visible(fn (?Monitor $record, Get $get) => $record || $get('type') === MonitorType::PULSE->value),
                        ])
                            ->visible(fn (Get $get) => $get('type') === MonitorType::PULSE->value)
                            ->columnSpanFull()
                            ->hidden(fn (Get $get) => $get('type') !== MonitorType::PULSE->value),
                        Forms\Components\Placeholder::make('pulse_info')
                            ->label('Pulse Information')
                            ->content('A token will be generated automatically when you save this monitor.')
                            ->helperText('You\'ll see the full URL with token after saving')
                            ->visible(fn (Get $get, ?Monitor $record) => $get('type') === MonitorType::PULSE->value && (!$record || !$record->address))
                            ->hidden(fn (Get $get, ?Monitor $record) => $get('type') !== MonitorType::PULSE->value || ($record && $record->address)),
                        Forms\Components\TextInput::make('curl_example')
                            ->dehydrated(false)
                            ->label('CURL Command')
                            ->readOnly()
                            ->disabled()
                            ->formatStateUsing(function (?Monitor $record, Get $get) {
                                // If we're looking at an existing record with a token
                                if ($record && $record->type === MonitorType::PULSE && $record->address) {
                                    $token = $get('address');
                                    $tokenParam = $token ? $token : 'YOUR_TOKEN';
                                    return "curl -X POST " . url('/api/pulse/' . $record->id) . "?token=" . $tokenParam;
                                }
                                // If we're creating a new record
                                if (!$record && $get('type') === MonitorType::PULSE->value) {
                                    return 'The example commands will be available after creating the monitor';
                                }
                                return 'Generate a token first to see example commands';
                            })
                            ->helperText('Add one of these commands to your cron job')
                            ->visible(fn (Get $get) => $get('type') === MonitorType::PULSE->value)
                            ->hidden(fn (Get $get) => $get('type') !== MonitorType::PULSE->value)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('copy_curl')
                                    ->label('Copy CURL')
                                    ->icon('heroicon-o-clipboard')
                                    ->action(fn () => Notification::make()
                                        ->title('CURL copied')
                                        ->body('The CURL command has been copied to your clipboard.')
                                        ->success()
                                        ->send())
                                    ->extraAttributes(fn ($state) => [
                                        'data-copy' => $state,
                                        'onclick' => 'navigator.clipboard.writeText(this.dataset.copy);',
                                    ])
                            ),
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
                        Forms\Components\Select::make('alerts')
                            ->helperText('Alerts to send when the monitor is down')
                            ->multiple()
                            ->relationship('alerts', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('user_id', auth()->id()))
                            ->preload(),
                        Forms\Components\Toggle::make('auto_create_update')
                            ->label('Post update when anomaly is detected')
                            ->helperText('Automatically create an update once an anomaly is detected (threshold reached) on the status pages where this monitor is being shown.')
                            ->default(true)
                            ->hintAction(
                                Forms\Components\Actions\Action::make('customize_text')
                                ->modalHeading('Customize update text')
                                ->modalFooter(fn () => new HtmlString('<div class="text-sm text-gray-500">Use the following variables in your update text: <code>:monitor_name</code>, <code>:monitor_address</code>, <code>:monitor_type</code></div>'))
                                ->form([
                                    Forms\Components\TextInput::make('update_values.title')
                                        ->label('Update title')
                                        ->helperText('The title of the update that will be posted when an anomaly is detected.')
                                        ->default(':monitor_name is experiencing issues'),
                                    Forms\Components\MarkdownEditor::make('update_values.content')
                                        ->label('Update content')
                                        ->helperText('The content of the update that will be posted when an anomaly is detected.')
                                        ->default("Our automated monitoring & alerting system has detected that :monitor_name is experiencing issues. Because of these issues, we've created this update to keep you informed.\n\nOur team has been notified and is investigating. We apologize for the inconvenience."),
                                ])
                                ->fillForm(function (?Monitor $record) {
                                    if (!$record) {
                                        return [];
                                    }
                                    
                                    return [
                                        'update_values' => [
                                            'title' => $record->update_values['title'] ?? ':monitor_name is experiencing issues',
                                            'content' => $record->update_values['content'] ?? "Our automated monitoring & alerting system has detected that :monitor_name is experiencing issues. Because of these issues, we've created this update to keep you informed.\n\nOur team has been notified and is investigating. We apologize for the inconvenience.",
                                        ],
                                    ];
                                })
                                ->action(function (?Monitor $record, array $data, Forms\Set $set) {
                                    if ($record) {
                                        $record->update([
                                            'update_values' => [
                                                'title' => $data['update_values']['title'],
                                                'content' => $data['update_values']['content'],
                                            ],
                                        ]);
                                    } else {
                                        $set('update_values', [
                                            'title' => $data['update_values']['title'],
                                            'content' => $data['update_values']['content'],
                                        ]);
                                    }
                                })
                                ),

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
                Tables\Actions\Action::make('copy_pulse_url')
                    ->label('Copy Pulse URL')
                    ->icon('heroicon-o-clipboard')
                    ->color('gray')
                    ->visible(fn (Monitor $record) => $record->type === MonitorType::PULSE)
                    ->action(function (Monitor $record) {
                        // Generate a new token and notify the user
                        $token = $record->generatePulseToken();
                        $fullUrl = url('/api/pulse/' . $record->id) . '?token=' . $token;
                        
                        Notification::make()
                            ->title('New Pulse URL Generated')
                            ->body("Your check-in URL with new token:\n```\n{$fullUrl}\n```\n\nCopy this URL now - you won't see the token again.")
                            ->warning()
                            ->persistent()
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('copy_url')
                                    ->label('Copy URL')
                                    ->icon('heroicon-o-clipboard')
                                    ->action(fn () => null)
                                    ->extraAttributes([
                                        'x-on:click' => 'navigator.clipboard.writeText("'.$fullUrl.'"); $tooltip("URL copied")',
                                    ]),
                            ])
                            ->send();
                    }),
            ])
            ->emptyStateHeading('Start monitoring your website')
            ->emptyStateDescription('Set up your first monitor to check the status of your website, API or other service.')
            ->emptyStateIcon('heroicon-o-heart')
            ->emptyStateActions([
                \Filament\Tables\Actions\CreateAction::make()
                    ->label('Create a monitor')
                    ->icon('heroicon-o-plus'),
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
