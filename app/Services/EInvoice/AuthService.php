<?php

namespace App\Services\EInvoice;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AuthService
{
    protected string $base_url;

    protected ?string $client_id;

    protected ?string $client_secret;

    protected string $scope;

    protected string $grant_type;

    protected string $token_cache_key;

    protected int $token_expiry_buffer_seconds;

    protected ?array $last_error = null;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->base_url = config('services.einvoice.base_url');
        $this->client_id = config('services.einvoice.client_id');
        $this->client_secret = config('services.einvoice.client_secret1');
        $this->scope = config('services.einvoice.scope');
        $this->grant_type = 'client_credentials';
        $this->token_cache_key = config('services.einvoice.token_cache_key');
        $this->token_expiry_buffer_seconds = config('services.einvoice.token_expiry_buffer_seconds');
        if (empty($this->client_id) || empty($this->client_secret)) {
            throw new Exception('Client ID or Client Secret is missing');
        }
    }

    /**
     * Example method for the service.
     */
    public function getAccessToken(): ?string
    {
        $cachedToken = Cache::get($this->token_cache_key);
        if (!$cachedToken || !is_array($cachedToken) || !array_key_exists('token', $cachedToken)) {
            $token = $this->refreshAccessToken();
            return $token;
        }

        return $cachedToken['token'];
    }

    public function refreshAccessToken(): ?string
    {
        $cachedToken = Cache::get($this->token_cache_key);

        $expired_at = $cachedToken['expires_at'] ?? null;
        try {
            if (
                empty($expired_at) || !empty($expired_at) && now()->greaterThan($expired_at)
            ) {

                $response = Http::asForm()->post($this->base_url . '/connect/token', [
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'grant_type' => $this->grant_type,
                    'scope' => $this->scope,
                ]);
                $response->throw()->json();
                $tokenData = $response->json();
                $accessToken = $tokenData['access_token'];
                $expiresIn = $tokenData['expires_in'];
                Cache::put($this->token_cache_key, [
                    'token' => $accessToken,
                    'expires_at' => now()->addSeconds($expiresIn - $this->token_expiry_buffer_seconds),
                ], $expiresIn);

                return $accessToken;
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $response = $e->response;
            $this->lastError = [
                'status' => $response->status(),
                'data' => [
                    'success' => false,
                    'message' => $response->json()['error'],
                    'details' => $response->json(),
                ],
            ];

            return 0;
        } catch (Exception $e) {
            $details = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];
            $this->lastError = [
                'status' => $e->getCode(),
                'data' => [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'details' => $details,
                ],
            ];
        }

        return $cachedToken['token'];
    }

    public function getLastError(): ?array
    {
        return $this->last_error;
    }
}
