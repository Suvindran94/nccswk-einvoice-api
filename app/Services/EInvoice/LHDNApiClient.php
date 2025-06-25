<?php

namespace App\Services\EInvoice;

use Illuminate\Support\Facades\Http;

class LHDNApiClient
{
    /**
     * Create a new service instance.
     *
     * @return void
     */

    public function __construct()
    {

    }

    /**
     * Example method for the service.
     *
     * @param  string  $message
     */
    public function submitDocument($submissionData)
    {
        $authService = new AuthService;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $authService->getAccessToken(),
            'Content-Type' => 'application/json',
        ])->post(config('services.einvoice.base_url') . '/api/v1.0/documentsubmissions/', $submissionData);
        return $response;
    }

    public function getDocumentSubmission($uid)
    {
        $authService = new AuthService;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $authService->getAccessToken(),
            'Accept' => 'application/json',
            'Accept-Language' => 'en',
            'Content-type' => 'application/json',
        ])->get(config('services.einvoice.base_url') . '/api/v1.0/documentsubmissions/' . $uid);

        return $response;
    }

    public function getRecentDocuments($params)
    {
        $authService = new AuthService;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $authService->getAccessToken(),
            'Accept' => 'application/json',
            'Accept-Language' => 'en',
            'Content-type' => 'application/json',
        ])->get(config('services.einvoice.base_url') . '/api/v1.0/documents/recent', $params);
        return $response;
    }

    public function getDocument(string $uuid)
    {
        $authService = new AuthService;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $authService->getAccessToken(),
            'Accept' => 'application/json',
            'Accept-Language' => 'en',
            'Content-type' => 'application/json',
        ])->get(config('services.einvoice.base_url') . '/api/v1.0/documents/' . $uuid . '/raw');
        return $response;
    }

    public function searchDocument(array $params)
    {
        $authService = new AuthService;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $authService->getAccessToken(),
            'Accept' => 'application/json',
            'Accept-Language' => 'en',
            'Content-type' => 'application/json',
        ])->get(config('services.einvoice.base_url') . '/api/v1.0/documents/search', $params);
        return $response;
    }
}
