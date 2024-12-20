<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Landing\LandingComponent;
use App\Livewire\Subscription\SubscriptionComponent;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/subscription', SubscriptionComponent::class)
    ->name('subscription');

Route::get('/landing', LandingComponent::class)
    ->name('landing');

require __DIR__.'/auth.php';
