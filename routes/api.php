<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\CommunityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Hier werden alle API-Endpunkte für dein Backend definiert.
| Diese Routen werden automatisch mit dem Prefix '/api' geladen.
| Beispiel: http://127.0.0.1:8000/api/chats
|
*/

// 🔹 CHAT ROUTES
Route::prefix('chats')->group(function () {
    Route::get('/', [ChatController::class, 'index']);        // Alle Chats abrufen
    Route::get('/{chat}', [ChatController::class, 'show']);   // Einzelnen Chat anzeigen
    Route::post('/', [ChatController::class, 'store']);       // Chat erstellen
    Route::put('/{chat}', [ChatController::class, 'update']); // Chat aktualisieren
    Route::delete('/{chat}', [ChatController::class, 'destroy']); // Chat löschen

    // Nachrichten in Chats
    Route::get('/{chat}/messages', [ChatMessageController::class, 'index']); // Nachrichten anzeigen
    Route::post('/{chat}/messages', [ChatMessageController::class, 'store']); // Nachricht senden
});

// 🔹 COMMUNITY ROUTES
Route::prefix('communities')->group(function () {
    Route::get('/', [CommunityController::class, 'index']);           // Alle Communities abrufen
    Route::get('/{community}', [CommunityController::class, 'show']); // Einzelne Community anzeigen
    Route::post('/', [CommunityController::class, 'store']);          // Neue Community erstellen
    Route::delete('/{community}', [CommunityController::class, 'destroy']); // Community löschen

    // Mitgliederverwaltung
    Route::post('/{community}/users', [CommunityController::class, 'addUser']); // User hinzufügen
    Route::put('/{community}/users/{user}/role', [CommunityController::class, 'updateUserRole']); // Rolle ändern
    Route::delete('/{community}/users/{user}', [CommunityController::class, 'removeUser']); // User entfernen
});
