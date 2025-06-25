<?php

use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Middleware\CheckBearerToken;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/getToken', function () {
        return response()->json([
            'token' => Cache::get('einvoice.access_token'),
        ]);
    });
    Route::middleware(['api', 'check.bearer.token'])->group(function () {
        Route::post('document/submit', [DocumentController::class, 'store']);
        Route::post('document/delete', [DocumentController::class, 'delete']);
        Route::get('test', function () {
            return response()->json('test');
        });
    });

});
