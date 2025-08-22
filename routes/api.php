<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{DeviceController, TicketController, SettingsController, LogsController, IngestController, NotificationController};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// OAuth-protected reads
Route::middleware(['scopes:devices:read'])->get('/devices', [DeviceController::class, 'index']);
Route::middleware(['scopes:devices:read'])->get('/devices/{id}', [DeviceController::class, 'show']);
Route::middleware(['scopes:tickets:read'])->get('/tickets', [TicketController::class, 'index']);
Route::middleware(['scopes:tickets:read'])->get('/tickets/{id}', [TicketController::class, 'show']);
Route::middleware(['scopes:settings:read'])->get('/settings/resolve', [SettingsController::class, 'resolve']);
Route::middleware(['scopes:devices:read'])->get('/logs', [LogsController::class, 'index']);

// Ticket management (CRUD + live chat)
Route::middleware(['scopes:tickets:write'])->group(function () {
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::put('/tickets/{id}', [TicketController::class, 'update']);
    Route::post('/tickets/{id}/messages', [TicketController::class, 'sendMessage']);
});

// Notifications
Route::middleware(['scopes:notifications:read'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
});


// Device ingest (device header auth + rate limit)
Route::middleware(['device.auth', 'ingest.size', 'throttle:ingest'])->group(function () {
    Route::post('/ingest', [IngestController::class, 'store']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
