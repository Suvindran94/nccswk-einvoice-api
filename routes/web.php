<?php

use App\Http\Controllers\EInvoiceController;
use Illuminate\Support\Facades\Route;
use App\Services\EInvoice\PrefixService;
Route::get('/schedulerDashboard', [EInvoiceController::class, 'schedulerDashboard']);
