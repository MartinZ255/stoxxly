<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/', function () {
    return Inertia::render('Homepage');
})->name('home');

Route::get('/currencies', function () {
    return Inertia::render('Currencies');
})->name('currencies');

Route::get('/communitys', function () {
    return Inertia::render('Communitys');
})->name('communitys');

Route::get('/watchlist', function () {
    return Inertia::render('Watchlist');
})->name('watchlist');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';



use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->name('login');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
