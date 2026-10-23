<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GuideController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:10,1');
});

Route::middleware(['auth', 'app.data'])->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pulse', [DashboardController::class, 'pulse'])->name('pulse');
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');

    Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');

    Route::middleware('role:staff')->group(function () {
        Route::get('/contracts/create', [ContractController::class, 'create'])->name('contracts.create');
        Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store');
        Route::get('/contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
        Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');
    });

    Route::middleware('role:supervisor')->group(function () {
        Route::post('/contracts/{contract}/approve', [ContractController::class, 'approve'])->name('contracts.approve');
        Route::post('/contracts/{contract}/reject', [ContractController::class, 'reject'])->name('contracts.reject');
        Route::delete('/contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');
    });

    Route::get('/guides', [GuideController::class, 'user'])->name('guides.user');
    Route::get('/guides/technical', [GuideController::class, 'technical'])->name('guides.technical');

    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/contracts/{contract}/documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
    Route::get('/contracts/{contract}/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
});
