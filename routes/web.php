<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('clients.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('clients', ClientController::class);
    Route::resource('documents', DocumentController::class);
    Route::post('documents/{document}/convert-to-invoice', [DocumentController::class, 'convertToInvoice'])
        ->name('documents.convert-to-invoice');
    Route::post('documents/{document}/payments', [PaymentController::class, 'store'])
        ->name('documents.payments.store');
    Route::delete('documents/{document}/payments/{payment}', [PaymentController::class, 'destroy'])
        ->name('documents.payments.destroy');
    Route::get('documents/{document}/pdf', [DocumentController::class, 'downloadPdf'])
        ->name('documents.pdf');
});

require __DIR__.'/auth.php';
