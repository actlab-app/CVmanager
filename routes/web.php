<?php

use App\Http\Middleware\TrackReferenceToken;
use App\Livewire\Admin\AboutManager;
use App\Livewire\Admin\ContactManager;
use App\Livewire\Admin\CvManager;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\PortfolioEditor;
use App\Livewire\Admin\PortfolioManager;
use App\Livewire\Admin\ReferenceTokenManager;
use App\Livewire\Admin\Settings\Appearance;
use App\Livewire\Admin\Settings\Password;
use App\Livewire\Admin\Settings\Profile;
use App\Livewire\Admin\Settings\TwoFactor;
use App\Livewire\Admin\TechnologyManager;
use App\Livewire\Web\About;
use App\Livewire\Web\Contact;
use App\Livewire\Web\Cv;
use App\Livewire\Web\PortfolioIndex;
use App\Livewire\Web\PortfolioProject;
use App\Support\ReferenceUrl;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/login', fn () => redirect('/admin'));
Route::view('/reference-token-required', 'reference-token-required')
    ->name('reference-token.required');

Route::middleware([TrackReferenceToken::class])->group(function () {
    Route::get('/', fn () => redirect(ReferenceUrl::route('cv', [], false)))->name('home');
    Route::get('/about', About::class)->name('about');
    Route::get('/cv', Cv::class)->name('cv');
    Route::get('/cv/modern', fn () => redirect(ReferenceUrl::route('cv', [], false)));
    Route::get('/contact', Contact::class)->name('contact');
    Route::get('/portfolio', PortfolioIndex::class)->name('portfolio.index');
    Route::get('/portfolio/{project:slug}', PortfolioProject::class)->name('portfolio.show');
});

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('cv-manager', CvManager::class)->name('cv-manager');
    Route::get('about-manager', AboutManager::class)->name('about-manager');
    Route::get('contact-manager', ContactManager::class)->name('contact-manager');
    Route::get('portfolio-manager', PortfolioManager::class)->name('portfolio-manager.index');
    Route::get('portfolio-manager/create', PortfolioEditor::class)->name('portfolio-manager.create');
    Route::get('portfolio-manager/{project}/edit', PortfolioEditor::class)->name('portfolio-manager.edit');
    Route::get('technology-manager', TechnologyManager::class)->name('technology-manager.index');
    Route::get('reference-token-manager', ReferenceTokenManager::class)->name('reference-token-manager.index');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    if (Features::canManageTwoFactorAuthentication()) {
        Route::get('settings/two-factor', TwoFactor::class)
            ->middleware(
                when(
                    Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                    ['password.confirm'],
                    [],
                ),
            )
            ->name('two-factor.show');
    }
});
