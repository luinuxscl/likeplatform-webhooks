<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use LikePlatform\Webhooks\Http\Controllers\WebhookEndpointController;

Route::middleware(['web', 'auth', 'verified'])->prefix('webhooks')->name('webhooks.')->group(function (): void {
    Route::get('/', [WebhookEndpointController::class, 'index'])->name('index');
    Route::get('/create', [WebhookEndpointController::class, 'create'])->name('create');
    Route::post('/', [WebhookEndpointController::class, 'store'])->name('store');
    Route::get('/{id}', [WebhookEndpointController::class, 'show'])->name('show');
    Route::post('/{id}/toggle', [WebhookEndpointController::class, 'toggle'])->name('toggle');
    Route::delete('/{id}', [WebhookEndpointController::class, 'destroy'])->name('destroy');
});
