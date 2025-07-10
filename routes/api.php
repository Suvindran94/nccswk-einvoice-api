<?php

use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\TinController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::middleware(['api', 'check.bearer.token'])->group(function () {
        Route::post('document/submit', [DocumentController::class, 'store']);
        Route::post('document/delete', [DocumentController::class, 'delete']);
        Route::post('tin/validation', [TinController::class, 'validation']);
    });

});
