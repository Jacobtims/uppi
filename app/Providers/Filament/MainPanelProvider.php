<?php

namespace App\Providers\Filament;

use App\Filament\Resources\PersonalAccessTokenResource;
use App\Filament\Widgets\ActiveAnomalies;
use App\Filament\Widgets\AnomaliesPerMonitor;
use App\Filament\Widgets\ResponseTime;
use App\Filament\Widgets\StatusWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class MainPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('main')
            ->path('')
            ->brandLogo(fn () => asset('logo.svg'))
            ->brandLogoHeight('2rem')
            ->favicon(fn () => asset('favicon.png'))
            ->login()
            ->colors([
                'primary' => Color::Red,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->darkMode(false)
            ->topNavigation()
            ->registration()
            ->profile()
            ->passwordReset()
            ->emailVerification()
            ->widgets([
                StatusWidget::class,
                AccountWidget::class,
                ResponseTime::class,
                AnomaliesPerMonitor::class,
                ActiveAnomalies::class,

            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->spa()
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn() => view('footer')
            )->userMenuItems([
                MenuItem::make()
                    ->label('Connections')
                    ->url(fn (): string => PersonalAccessTokenResource::getUrl())
                    ->icon('heroicon-o-device-phone-mobile'),
            ]);
            ;
    }
}
