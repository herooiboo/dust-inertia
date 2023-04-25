<?php

namespace Dust\Http\Responses;

use Illuminate\Http\JsonResponse;

class ErrorResponse extends JsonResponse
{
    public function __construct(
        string $message = 'Something went wrong.',
        array $data = [],
        int $status = 500,
        array $headers = [],
        int $options = 0,
        bool $json = false,
    ) {
        parent::__construct(['data' => $data, 'message' => $message], $status, $headers, $options, $json);
    }
}
