<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])->navigationGroups([

                NavigationGroup::make('Shop'),

                NavigationGroup::make('Catalog'),

                NavigationGroup::make('Attributes')
                    ->collapsed(),

                NavigationGroup::make('Settings')
                    ->collapsed(),

                NavigationGroup::make('Filament Shield')
                    ->collapsed(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])->plugins([
                FilamentShieldPlugin::make(), // <--- INJECT THE SHIELD COMPONENT HERE
            ])->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('
                <style>
                    /* 1. Track setup */
                    aside .fi-sidebar-nav::-webkit-scrollbar {
                        width: 6px;
                    }
                    aside .fi-sidebar-nav::-webkit-scrollbar-track {
                        background: transparent;
                    }

                    /* 2. WebKit Thumb with smooth fade transition */
                    aside .fi-sidebar-nav::-webkit-scrollbar-thumb {
                        background-color: transparent;
                        border-radius: 9999px;
                        transition: background-color 0.3s ease-in-out;
                    }

                    /* 3. Fade in thumb when hovering over sidebar */
                    aside:hover .fi-sidebar-nav::-webkit-scrollbar-thumb {
                        background-color: #27272a;
                    }

                    /* 4. Slightly brighter when hovering directly over scrollbar */
                    aside .fi-sidebar-nav::-webkit-scrollbar-thumb:hover {
                        background-color: #3f3f46;
                    }

                    /* 5. Firefox smooth transition support */
                    aside .fi-sidebar-nav {
                        scrollbar-width: thin;
                        scrollbar-color: transparent transparent;
                        transition: scrollbar-color 0.3s ease-in-out;
                    }
                    aside:hover .fi-sidebar-nav {
                        scrollbar-color: #27272a transparent;
                    }
                </style>
            ')
            );
    }
}
