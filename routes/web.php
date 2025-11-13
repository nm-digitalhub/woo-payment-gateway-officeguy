<?php

use Illuminate\Support\Facades\Route;
use NmDigitalHub\SumitPayment\Controllers\PaymentController;
use NmDigitalHub\SumitPayment\Controllers\TokenController;

Route::prefix('sumit-payment')->name('sumit-payment.')->group(function () {
    
    // Payment routes
    Route::post('/process', [PaymentController::class, 'processPayment'])->name('process');
    Route::get('/redirect', [PaymentController::class, 'handleRedirect'])->name('redirect');
    Route::post('/refund', [PaymentController::class, 'processRefund'])->name('refund');

    // Token routes (require authentication)
    Route::middleware('auth')->group(function () {
        Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
        Route::post('/tokens', [TokenController::class, 'create'])->name('tokens.create');
        Route::delete('/tokens/{tokenId}', [TokenController::class, 'destroy'])->name('tokens.destroy');
        Route::post('/tokens/{tokenId}/set-default', [TokenController::class, 'setDefault'])->name('tokens.set-default');
    });
});
