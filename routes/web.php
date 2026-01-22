<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\IncomingDocumentController;
use App\Http\Controllers\Admin\OutgoingDocumentController;
use App\Http\Controllers\Office\DocumentInboxController;
use App\Http\Controllers\DocumentFileController;

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ADMIN — INCOMING DOCUMENTS
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin/incoming')->group(function () {
        Route::post('/', [IncomingDocumentController::class, 'store'])
            ->name('admin.incoming.store');

        Route::post('{document}/log', [IncomingDocumentController::class, 'log'])
            ->name('admin.incoming.log');

        Route::post('{document}/forward', [IncomingDocumentController::class, 'forward'])
            ->name('admin.incoming.forward');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN — OUTGOING DOCUMENTS
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin/outgoing')->group(function () {
        Route::post('{document}/log', [OutgoingDocumentController::class, 'log'])
            ->name('admin.outgoing.log');

        Route::post('{document}/transmit', [OutgoingDocumentController::class, 'transmit'])
            ->name('admin.outgoing.transmit');

        Route::post('{document}/archive', [OutgoingDocumentController::class, 'archive'])
            ->name('admin.outgoing.archive');
    });

    /*
    |--------------------------------------------------------------------------
    | OFFICE — DOCUMENT INBOX
    |--------------------------------------------------------------------------
    */
    Route::prefix('office/documents')->group(function () {
        Route::get('/', [DocumentInboxController::class, 'index'])
            ->name('office.documents.index');

        Route::post('{document}/acknowledge', [DocumentInboxController::class, 'acknowledge'])
            ->name('office.documents.acknowledge');

        Route::post('{document}/reply', [DocumentInboxController::class, 'prepareReply'])
            ->name('office.documents.reply');
    });


    /*
    |--------------------------------------------------------------------------
    | DOCUMENT - DOWNLOAD FILE
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth'])->group(function () {

        Route::get('/documents/{document}/download', [DocumentFileController::class, 'download'])
            ->name('documents.download');

        Route::get('/documents/{document}/view', [DocumentFileController::class, 'view'])
            ->name('documents.view');
    });

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT - PREVIEW ATTACHMENT
    |--------------------------------------------------------------------------
    */
    Route::get('/attachments/{attachment}/preview', [DocumentFileController::class, 'preview'])
        ->name('attachments.preview')
        ->middleware('auth');
        
});

require __DIR__.'/auth.php';

