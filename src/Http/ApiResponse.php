<?php


namespace App\Http;


use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    public function __construct(string $message, $data = null, array $errors = [], int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct($this->format($message, $data, $errors), $status, $headers, $json);
    }

    protected function format(string $message, $data = null, array $errors = []): array
    {
        if ($data === null) {
            $data = [];
        }

        $response = compact('message', 'data');

        if ($errors) {
            $response['errors'] = $errors;
        }

        return $response;
    }
}
