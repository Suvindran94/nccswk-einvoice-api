<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TinRequest;
use App\Services\EInvoice\TinService;

class TinController extends Controller
{
    public function validation(TinRequest $request)
    {
        $response = TinService::validateTin($request->input('tin'), $request->input('id_type'), $request->input('id_value'));
        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => $response['message'],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $response['message']
            ], $response['status_code']);
        }
    }
}
