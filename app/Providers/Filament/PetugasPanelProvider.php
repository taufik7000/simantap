<?php

namespace App\Providers\Filament;

use App\Filament\Petugas\Resources\PermohonanResource;
use Filament\Navigation\NavigationItem;
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
use Illuminate\Support\Facades\Vite;

class PetugasPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('petugas')
            ->path('petugas')
            ->renderHook(
                'panels::head.end',
                fn (): string => view('filament.hooks.custom-assets')->render(),
            )
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->navigationItems([
                NavigationItem::make('Permohonan Terverifikasi')
                    ->label('Permohonan Terverifikasi')
                    // Pastikan label ini sama persis dengan 'navigationLabel' di PermohonanResource.php
                    ->parentItem('Permohonan Warga') 
                    ->icon('heroicon-o-check-badge')
                    ->url('/petugas/permohonans?tableFilters[status][values][0]=proses_entri&tableFilters[status][values][1]=entri_data_selesai&tableFilters[status][values][2]=menunggu_persetujuan&tableFilters[status][values][3]=disetujui&tableFilters[status][values][4]=dokumen_diterbitkan&tableFilters[status][values][5]=proses_pengiriman&tableFilters[status][values][6]=selesai')
                    ->sort(1),
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('5s')
            ->discoverResources(in: app_path('Filament/Petugas/Resources'), for: 'App\\Filament\\Petugas\\Resources')
            ->discoverPages(in: app_path('Filament/Petugas/Pages'), for: 'App\\Filament\\Petugas\\Pages')
            ->discoverWidgets(in: app_path('Filament/Petugas/Widgets'), for: 'App\\Filament\\Petugas\\Widgets')
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
