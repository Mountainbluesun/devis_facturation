<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});


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
