<?php
namespace Utils;

use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseHelper
{
    public static function success($data = [], string $message = 'OK', int $code = 200): void
    {
        self::sendJson([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], $code); 
    }

    public static function error(string $message = 'Something went wrong', int $code = 500, $errors = []): void
    {
        self::sendJson([
            'success' => false,
            'message' => $message,
            'errors'  => $errors
        ], $code);
    }

    private static function sendJson($data, int $statusCode = 200): void
    {
        $response = new JsonResponse($data, $statusCode);
        $response->send();
        exit;
    }
}