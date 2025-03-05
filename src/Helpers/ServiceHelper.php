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

    public static function sendGetRequest(string $service, array $params = [])
    {
        $base_url = self::getUrl('base_url');
        $endpoint = $base_url . self::getUrl($service);

        if (!$base_url || !$endpoint) {
            return Response::sendError("Service not found.");
        }

        $client = new Client([
            'base_uri' => $base_url,
            'timeout' => 10.0,
        ]);

        try {
            $response = $client->request('GET', $endpoint, [
                'query' => $params
            ]);

            $decodedResponse = json_decode($response->getBody()->getContents(), true);
            return $decodedResponse;
        } catch (RequestException $e) {
            return Response::sendError("An unexpected error occurred: " . $e->getMessage());
        }
    }
}
