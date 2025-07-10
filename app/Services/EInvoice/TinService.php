<?php

namespace App\Services\EInvoice;
use App\Services\EInvoice\LHDNApiClient;
class TinService
{
    public static function validateTin(string $tin, string $idType, string $idValue)
    {
        $lhdnApiClient = new LHDNApiClient();
        $response = $lhdnApiClient->validateTin($tin, $idType, $idValue);
        $data = $response->json();
        $statusCode = $response->status();
        switch ($statusCode) {
            case 200:
                return [
                    'success' => true,
                    'status' => 'valid',
                    'data' => $data,
                    'status_code' => $statusCode,
                    'message' => 'TIN validation successful. The provided TIN and ID combination is valid.'
                ];

            case 400:
                return [
                    'success' => false,
                    'status' => 'invalid_format',
                    'status_code' => $statusCode,
                    'message' => 'Invalid input format. Please check your TIN number, registration type, and ID number format.',
                    'error_type' => 'validation_error',
                    'data' => $data
                ];

            case 404:
                return [
                    'success' => false,
                    'status' => 'not_found',
                    'status_code' => $statusCode,
                    'message' => 'TIN and ID combination not found. The provided TIN and identification number do not match LHDN records.',
                    'error_type' => 'not_found',
                    'data' => $data
                ];

            case 401:
                return [
                    'success' => false,
                    'status' => 'unauthorized',
                    'status_code' => $statusCode,
                    'message' => 'Authentication failed. Please contact system administrator.',
                    'error_type' => 'auth_error'
                ];

            case 500:
                return [
                    'success' => false,
                    'status' => 'server_error',
                    'status_code' => $statusCode,
                    'message' => 'LHDN service is temporarily unavailable. Please try again later.',
                    'error_type' => 'server_error'
                ];

            default:
                return [
                    'success' => false,
                    'status' => 'unknown_error',
                    'status_code' => $statusCode,
                    'message' => "Unexpected error occurred (HTTP {$statusCode}). Please try again or contact support.",
                    'error_type' => 'unknown_error',
                    'data' => $data
                ];
        }
    }
}