<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;
use App\Filament\Petugas\Resources\PermohonanResource;
use App\Filament\Petugas\Resources\KartuKeluargaResource;
use App\Filament\Petugas\Resources\UserResource;
use Illuminate\Support\Facades\Vite;

class KadisPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('kadis')
            ->path('kadis')
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->renderHook(
                'panels::head.end',
                fn (): string => view('filament.hooks.custom-assets')->render(),
            )
            ->discoverResources(in: app_path('Filament/Kadis/Resources'), for: 'App\\Filament\\Kadis\\Resources')
            ->discoverPages(in: app_path('Filament/Kadis/Pages'), for: 'App\\Filament\\Kadis\\Pages')
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Manajemen Permohonan')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Kependudukan')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Kependudukan')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Pusat Bantuan')
                    ->collapsible(false),
            ])
            ->resources([
                PermohonanResource::class,
                KartuKeluargaResource::class,
                UserResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Kadis/Widgets'), for: 'App\\Filament\\Kadis\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
