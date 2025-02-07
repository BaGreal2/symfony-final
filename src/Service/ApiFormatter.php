<?php
// src/Service/ApiFormatter.php
namespace App\Service;

class ApiFormatter
{
    /**
     * Format the response data.
     *
     * @param mixed $data The data payload
     * @param string $status Either 'success' or 'error'
     * @param string|null $message Optional message (used when status is error)
     * @return array
     */
    public function format($data, string $status = 'success', ?string $message = null): array
    {
        $response = [
            'status' => $status,
            'data' => $data,
        ];

        if ('error' === $status && $message) {
            $response['message'] = $message;
        }

        return $response;
    }
}
