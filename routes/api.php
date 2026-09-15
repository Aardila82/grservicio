<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── AUTH (public) ──────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('login',  [AuthController::class, 'login']);
    });

    // 🌐 PUBLIC INVOICE VIEW (UUID is secure enough)
    Route::get('invoices/{invoice}/view', [InvoiceController::class, 'publicView'])
        ->name('invoices.public.view');

    // 🔒 PROTECTED ────────────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me',     [AuthController::class, 'me']);
        });

        // Dashboard
        Route::prefix('dashboard')->group(function () {
            Route::get('stats',           [DashboardController::class, 'stats']);
            Route::get('recent-invoices', [DashboardController::class, 'recentInvoices']);
        });

        // Clients
        Route::apiResource('clients', ClientController::class);
        Route::get('clients/{id}/invoices', [ClientController::class, 'invoices']);

        // Invoices
        Route::apiResource('invoices', InvoiceController::class);
        Route::post('invoices/{id}/generate-pdf',  [InvoiceController::class, 'generatePdf']);
        Route::get('invoices/{id}/download-pdf',   [InvoiceController::class, 'downloadPdf']);
        Route::post('invoices/{id}/send-whatsapp', [InvoiceController::class, 'sendWhatsApp']);
        Route::get('invoices/{id}/logs',           [InvoiceController::class, 'logs']);

        // Settings
        Route::get('settings',             [SettingsController::class, 'show']);
        Route::put('settings',             [SettingsController::class, 'update']);
        Route::post('settings/upload-logo', [SettingsController::class, 'uploadLogo']);
    });
});
