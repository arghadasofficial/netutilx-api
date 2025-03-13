<?php

namespace Argha\NetutilxApi\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ServiceHelper
{
    private static array $servicesUrl = [
        'base_url' => 'https://netutilx.grow10x.business/service/',
        'dnsx' => 'dnsx/dnsx.php',
    ];

    public static function getUrl(string $key)
    {
        return self::$servicesUrl[$key] ?? null;
    }

    public static function sendGetRequest(string $service, array $params = []): array
    {
        $base_url = self::getUrl('base_url');
        $endpoint = $base_url . self::getUrl($service);

        if (!$base_url || !$endpoint) {
            return [
                "success" => false,
                "error" => "Service not found.",
                "endpoint" => $endpoint,
                "request" => $params,
                "response" => null,
                "decoded_response" => null,
                "status_code" => null,
            ];
        }

        $client = new Client([
            'base_uri' => $base_url,
            'timeout' => 10.0,
        ]);

        try {
            $response = $client->request('GET', $endpoint, [
                'query' => $params
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();

            // Ensure response is properly decoded
            $decodedResponse = json_decode($responseBody, true);

            // Check if the decoded response is valid JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                $decodedResponse = null; // Mark as invalid JSON
            }

            // Determine success based on API response structure
            $success = is_array($decodedResponse) && isset($decodedResponse['success'])
                ? (bool) $decodedResponse['success']
                : ($statusCode >= 200 && $statusCode < 300); // Fallback to HTTP status check

            return [
                "success" => $success,
                "endpoint" => $endpoint,
                "request" => $params,
                "response" => $decodedResponse ?? $responseBody, // Ensure proper JSON structure
                "decoded_response" => $decodedResponse,
                "status_code" => $statusCode,
            ];
        } catch (RequestException $e) {
            // Handle cases where the response is null (e.g., connection timeout)
            $responseBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null;
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : null;

            // Ensure error response is valid JSON
            $decodedErrorResponse = json_decode($responseBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $decodedErrorResponse = null;
            }

            return [
                "success" => false,
                "error" => "Request failed: " . $e->getMessage(),
                "endpoint" => $endpoint,
                "request" => $params,
                "response" => $decodedErrorResponse ?? $responseBody, // Avoid double encoding
                "decoded_response" => $decodedErrorResponse,
                "status_code" => $statusCode,
            ];
        }
    }
}
