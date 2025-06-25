<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

class ErrorResponse implements Responsable
{
    protected int $http_code;

    protected array $errors;

    public function __construct(int $http_code, string|array $message, array $details)
    {
        $this->http_code = $http_code;
        $this->errors = [
            'success' => false,
            'message' => $message,
            'errors' => $details,
        ];
    }

    public function toResponse($request): JsonResponse
    {
        return response()->json(
            data: $this->errors,
            status: $this->http_code,
        );
    }
}
