<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyFavoriteController;
use App\Http\Controllers\CompanySubscriptionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Laravel\Fortify\Features;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ==================== Guest Routes ====================

Route::middleware('guest')->group(function () {
    // Landing Page
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
        ]);
    })->name('home');
});

// ==================== Locale Switching ====================

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);

        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
    }

    return redirect()->back();
})->name('lang.switch');

// ==================== Authenticated Routes ====================

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Trading Signals (Placeholder routes - implement later)
    Route::prefix('signals')->name('signals.')->group(function () {
        Route::get('/', function () {
            return Inertia::render('Signals/Index');
        })->name('index');
    });

    // Portfolio (Placeholder routes - implement later)
    Route::prefix('portfolio')->name('portfolio.')->group(function () {
        Route::get('/', function () {
            return Inertia::render('Portfolio/Index');
        })->name('index');
    });

    // Market Analysis (Placeholder routes - implement later)
    Route::prefix('market')->name('market.')->group(function () {
        Route::get('/', function () {
            return Inertia::render('Market/Index');
        })->name('index');
    });

    // Companies
    Route::get('companies', [CompanyController::class, 'index'])
        ->name('companies.index');

    Route::get('companies/favorites', [CompanyController::class, 'favorites'])
        ->name('companies.favorites');

    Route::get('companies/{company}', [CompanyController::class, 'show'])
        ->name('companies.show');

    Route::post('companies/{company}/favorite', [CompanyFavoriteController::class, 'store'])
        ->name('companies.favorite.store');

    Route::delete('companies/{company}/favorite', [CompanyFavoriteController::class, 'destroy'])
        ->name('companies.favorite.destroy');

    Route::post('companies/{company}/subscribe', [CompanySubscriptionController::class, 'store'])
        ->name('companies.subscription.store');

    Route::delete('companies/{company}/subscribe', [CompanySubscriptionController::class, 'destroy'])
        ->name('companies.subscription.destroy');

});

// ==================== Settings Routes ====================

require __DIR__.'/settings.php';
