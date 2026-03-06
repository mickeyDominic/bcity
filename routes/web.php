<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ModalController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('clients', ClientController::class);
Route::resource('contacts', ContactController::class);
Route::post('/clients/get-data', [ClientController::class, 'getRecords'])
    ->name('clients/get-data');
Route::post('/contacts/get-data', [ContactController::class, 'getRecords'])
    ->name('contacts/get-data');

Route::post('/clients/link-contacts', [ClientController::class, 'storeClientContacts'])
    ->name('clients/link-contacts');
Route::post('/clients/unlink-from-contact', [ClientController::class, 'unlinkFromContact'])
    ->name('clients/unlink-from-contact');
Route::post('/contacts/link-clients', [ContactController::class, 'storeContactClients'])
    ->name('contacts/link-clients');
Route::post('/contacts/unlink-from-client', [ContactController::class, 'unlinkFromClient'])
    ->name('contacts/unlink-from-client');

Route::post('/reusable-modal/get-modal-content', [ModalController::class, 'reusableModalContent'])
    ->name('reusable-modal/get-modal-content');

require __DIR__.'/auth.php';
