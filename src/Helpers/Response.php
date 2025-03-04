<?php
namespace Argha\NetutilxApi\Helpers;
class Response
{
    public static function sendSuccess($message, $data = [])
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    public static function sendError($message, $data = [])
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => $data
        ];
    }
}
