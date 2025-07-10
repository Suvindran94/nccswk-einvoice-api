<?php

use App\Http\Controllers\EInvoiceController;
use Illuminate\Support\Facades\Route;
use App\Models\EmailPermission;
Route::get('/schedulerDashboard', [EInvoiceController::class, 'schedulerDashboard']);
