<?php

use App\Http\Controllers\Recruitment\ApplicationController;
use App\Http\Controllers\Recruitment\LandingController;
use Illuminate\Support\Facades\Route;

Route::prefix('open-recruitment')
    ->name('open-recruitment.')
    ->group(function (): void {
        Route::get('/', LandingController::class)->name('landing');

        Route::get('/apply', [ApplicationController::class, 'create'])->name('apply');
        Route::post('/apply', [ApplicationController::class, 'store'])
            ->middleware(['recruitment.period.open', 'throttle:oprec-apply'])
            ->name('apply.store');

        Route::get('/success', [ApplicationController::class, 'success'])->name('success');

        Route::get('/track', fn () => redirect()->route('open-recruitment.landing'))
            ->name('track.login');
    });
