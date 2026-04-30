<?php

namespace App\Support\Api;

use Illuminate\Http\Request;

class ApiErrorResponse
{
    public static function make(
        Request $request,
        string $message,
        int $status,
        ?array $errors = null,
        ?string $code = null,
        ?array $extra = null
    ): array {
        return array_filter([
            'ok' => false,
            'message' => $message,
            'code' => $code,
            'errors' => $errors,
            'request_id' => $request->attributes->get('request_id'),
            'path' => $request->path(),
            'extra' => $extra,
        ], static fn ($value) => $value !== null);
    }
}
