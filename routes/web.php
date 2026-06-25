<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\DashboardComponent;
use App\Livewire\ReconciliationComponent;
use App\Livewire\HistoryComponent;
use App\Livewire\ProfileComponent;

use App\Livewire\PinScreenComponent;

Route::get('/locked', PinScreenComponent::class)->name('locked');

Route::middleware(['pin.protect'])->group(function () {
    Route::get('/', DashboardComponent::class)->name('dashboard');
    Route::get('/reconciliation', ReconciliationComponent::class)->name('reconciliation');
    Route::get('/history', HistoryComponent::class)->name('history');
    Route::get('/profile', ProfileComponent::class)->name('profile');
});
