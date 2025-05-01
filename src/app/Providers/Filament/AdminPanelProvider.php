<?php

namespace App\Providers\Filament;

use App\Enums\ApiPermission;
use App\Livewire\ProfileTimezoneComponent;
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->sidebarCollapsibleOnDesktop()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
                'gray' => Color::Stone,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->brandLogo(fn () => view('filament.admin.logo'))
            ->favicon(asset('images/logo.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //
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
            ->brandName('Proxima')
            //NOTE: only set this if the page is on that domain
            //->domain('proxima.goliath.hu')
            ->globalSearchKeyBindings(['CTRL+ALT+S', 'command+option+s'])
            ->globalSearchFieldKeyBindingSuffix()
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                GlobalSearchModalPlugin::make()
                    ->expandedUrlTarget(enabled: false),
                FilamentEditProfilePlugin::make()
                    ->shouldShowDeleteAccountForm(false)
                    ->setIcon('heroicon-o-user')
                    ->shouldShowBrowserSessionsForm(true)
                    ->shouldRegisterNavigation(false)
                    ->shouldShowSanctumTokens(
                        condition: true,
                        permissions: ApiPermission::toDescribedArray()
                    )
                    ->customProfileComponents([
                        ProfileTimezoneComponent::class,
                    ]),
                //TODO: only add github if github credentials are found!!!
                EasyFooterPlugin::make()
                    ->footerEnabled()
                    ->withFooterPosition('footer')
                    ->withSentence(new HtmlString('<img class="non-grayscale" src="'.asset('images/icon-512.png').'" alt="Proxima Backend Logo" width="20" height="20"> Proxima Backend'))
                    ->withGithub(showLogo: true, showUrl: true)
                    ->withLoadTime()
                    ->withLinks([
                        [
                            'title' => 'About',
                            'url' => 'https://proxima.goliath.hu',
                        ],
                        [
                            'title' => 'Docs',
                            'url' => 'https://proxima.goliath.hu/assets/docs/index.html',
                        ],
                    ]),
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn () => Auth::user()->name)
                    ->url(fn () => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle'),
            ])
        ;
    }
}
