<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StatusPageResource\Pages;
use App\Filament\Resources\StatusPageResource\RelationManagers\ItemsRelationManager;
use App\Models\StatusPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StatusPageResource extends Resource
{
    protected static ?string $model = StatusPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?int $navigationSort = 5;

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
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->prefix('https://uppi.dev/s/')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Toggle::make('is_enabled')
                            ->required()
                            ->default(true)
                            ->helperText('Enable or disable this status page'),
                    ])->columns(2),

                Forms\Components\Section::make('Appearance')
                    ->schema([
                        Forms\Components\FileUpload::make('logo_url')
                            ->image()
                            ->label('Logo')
                            ->disk('public')
                            ->directory('status-page-logos')
                            ->maxSize(1024)
                            ->helperText('Upload a logo for your status page (max 1MB)'),
                        Forms\Components\TextInput::make('website_url')
                            ->url()
                            ->maxLength(255)
                            ->helperText('Link to your main website'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('URL')
                    ->prefix('https://uppi.dev/s/')
                    ->copyable()
                    ->iconPosition(IconPosition::After)
                    ->icon('heroicon-o-link')
                    ->tooltip('Click to copy')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_enabled')
                    ->label('Status')
                    ->placeholder('All Status Pages')
                    ->trueLabel('Enabled Pages')
                    ->falseLabel('Disabled Pages'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('embed')
                    ->label('Embed')
                    ->modalHeading('Embed Status Page')
                    ->modalDescription('Copy and paste this code into your website to embed the status page.')
                    ->modalContent(fn ($record) => view('filament.resources.status-page.embed-code', [
                        'statusPage' => $record,
                    ]))
                    ->modalWidth('2xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStatusPages::route('/'),
            'create' => Pages\CreateStatusPage::route('/create'),
            'edit' => Pages\EditStatusPage::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Status Pages';
    }

    public static function getActions(): array
    {
        return [

        ];
    }
}
