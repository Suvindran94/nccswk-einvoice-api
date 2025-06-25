<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use App\Enums\DocumentResponseStatus;
class DocumentResponse implements Responsable
{
    protected int $http_code;

    protected array $message;

    public function __construct(int $http_code, bool $success, DocumentResponseStatus $status)
    {
        $this->http_code = $http_code;
        $this->message = [
            'success' => $success,
            'status' => $status->value,
        ];
    }

    public function toResponse($request): JsonResponse
    {
        return response()->json(
            data: $this->message,
            status: $this->http_code,
        );
    }
}
