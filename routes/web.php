<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\{
    ProfileController,
    IndicatorController,
    CommunityController,
    ChatController
};
use App\Models\Community;

/*
|--------------------------------------------------------------------------
| 🔹 Public Routes (ohne Login)
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => Inertia::render('Homepage'))->name('home');
Route::get('/currencies', fn() => Inertia::render('Currencies'))->name('currencies');
Route::get('/alerts', fn() => Inertia::render('Alerts'))->name('alerts');
Route::get('/settings', fn() => Inertia::render('PersonalSettings'))->name('settings');

Route::get('/indicators', [IndicatorController::class, 'index'])->name('indicators.index');
Route::get('/indicators/{slug}', [IndicatorController::class, 'show'])->name('indicators.show');

/*
|--------------------------------------------------------------------------
| 🔹 Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 🧭 Dashboard & Profile
    |--------------------------------------------------------------------------
    */
    //Route::get('/dashboard', fn() => Inertia::render('Dashboard'))->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | 🌐 Community Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/communitys', function () {
        $user = auth()->user();

        $communities = Community::withCount('users')
            ->with('users:id')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($c) use ($user) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'description' => $c->description,
                    'users_count' => $c->users_count,
                    'joined' => $user ? $c->users->contains($user->id) : false,
                ];
            });

        return Inertia::render('Communitys', [
            'communities' => $communities,
        ]);
    })->name('communitys');

    Route::post('/communitys', [CommunityController::class, 'store'])->name('communitys.store');
    Route::post('/communitys/{community}/join', [CommunityController::class, 'join'])->name('communitys.join');
    Route::delete('/communitys/{community}/leave', [CommunityController::class, 'leave'])->name('communitys.leave');

    /*
    |--------------------------------------------------------------------------
    | 💬 Chat Routes
    |--------------------------------------------------------------------------
    |
    | Enthält:
    | - CRUD für Chats
    | - Join/Leave
    | - Nachrichten (storeMessage)
    |   -> kompatibel mit deinem Frontend (Axios + Polling)
    |--------------------------------------------------------------------------
    */
    Route::prefix('chats')->name('chats.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::post('/', [ChatController::class, 'store'])->name('store');
        Route::get('/{chat}', [ChatController::class, 'show'])->name('show');
        Route::patch('/{chat}', [ChatController::class, 'update'])->name('update');
        Route::delete('/{chat}', [ChatController::class, 'destroy'])->name('destroy');

        Route::post('/{chat}/join', [ChatController::class, 'join'])->name('join');
        Route::delete('/{chat}/leave', [ChatController::class, 'leave'])->name('leave');

        // Nachricht speichern (vom Frontend verwendet)
        Route::post('/{chat}/messages', [ChatController::class, 'storeMessage'])->name('messages.store');
    });

    /*
    |--------------------------------------------------------------------------
    | 🧩 Community → Chat Mapping
    |--------------------------------------------------------------------------
    | Optional: Für den Fall, dass jede Community ihren eigenen Chat besitzt.
    */
    Route::get('/communitys/{community}/chat', [ChatController::class, 'createForCommunity'])
        ->name('community.chat');

    /*
    |--------------------------------------------------------------------------
    | 📊 Watchlist
    |--------------------------------------------------------------------------
    */
    Route::get('/watchlist', fn() => Inertia::render('Watchlist'))->name('watchlist');
});

/*
|--------------------------------------------------------------------------
| ❗️ Authentifizierung
|--------------------------------------------------------------------------
| Fortify/Breeze registrieren Login-, Register-, Reset-Routen automatisch.
| So werden doppelte Routennamen vermieden.
*/
require __DIR__.'/auth.php';
