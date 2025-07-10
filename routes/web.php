<?php

use App\Http\Controllers\EInvoiceController;
use Illuminate\Support\Facades\Route;
Route::get('/schedulerDashboard', [EInvoiceController::class, 'schedulerDashboard']);
