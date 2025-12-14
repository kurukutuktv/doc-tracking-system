<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// USER AUTH ROUTES
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// DOCUMENT ROUTES
Route::middleware(['auth'])->group(function () {
    Route::resource('documents', DocumentController::class);

    Route::post('documents/{document}/approve', [DocumentController::class, 'approve'])
        ->name('documents.approve');

    Route::post('documents/{document}/reject', [DocumentController::class, 'reject'])
        ->name('documents.reject');
});


Route::middleware(['auth'])->group(function () {
    Route::resource('documents', DocumentController::class);
    Route::post('documents/{document}/acknowledge', [DocumentController::class, 'acknowledge'])->name('documents.acknowledge');
    Route::post('documents/{document}/approve', [DocumentController::class, 'approve'])->name('documents.approve');
    Route::post('documents/{document}/reject', [DocumentController::class, 'reject'])->name('documents.reject');
});

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    });

// Route::get('/test-doc', function () {
//     return App\Models\Document::all();
// });



require __DIR__ . '/auth.php';
