<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EInvoiceController extends Controller
{
    public function schedulerDashboard()
    {
        return view("einvoice.toggle");
    }
}
