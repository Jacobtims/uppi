<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DeleteAccount extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithActions;
    protected static ?string $navigationIcon = 'heroicon-o-trash';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.delete-account';

    public ?array $data = [];

    public bool $isPasswordFilled = false;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('current_password')
                    ->label('Current Password')
                    ->password()
                    ->required()
                    ->rules(['required'])
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $this->isPasswordFilled = !empty($state);
                    })
                    ->helperText('Please enter your current password to confirm account deletion.'),
            ])
            ->statePath('data');
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label('Delete Account')
            ->color('danger')
            ->disabled(fn () => !$this->isPasswordFilled)
            ->requiresConfirmation()
            ->modalHeading('We are about to delete your account')
            ->modalDescription('Are you sure you want to delete your account? All your data, including monitors, alerts, and settings will be permanently deleted. This is the final confirmation before your account is deleted. This action is irreversible.')
            ->modalSubmitActionLabel('Yes, delete my account')
            ->action(function () {
                $data = $this->form->getState();

                if (!Hash::check($data['current_password'], auth()->user()->password)) {
                    Notification::make()
                        ->title('Incorrect password')
                        ->danger()
                        ->send();

                    return;
                }

                $user = auth()->user();
                $user->monitors()->delete();
                $user->alerts()->delete();
                $user->statusPages()->delete();
                $user->tokens()->delete();
                $user->checks()->delete();
                Auth::logout();
                $user->delete();

                Notification::make()
                    ->title('Account deleted successfully')
                    ->success()
                    ->send();

                return redirect('/');
            });
    }

}
