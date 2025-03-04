<?php

namespace Argha\NetutilxApi\Controllers;

use Argha\NetutilxApi\Helpers\Response;
use Argha\NetutilxApi\Controllers\APIKeyController;

class BaseAPIController {

    // fixed the error in the code
    protected $userId = null;

    public function __construct() {
        // Extract API Key from headers
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $apiKey = $headers['X-API-KEY'] ?? ($_SERVER['HTTP_X_API_KEY'] ?? null);

        if (!$apiKey) {
            echo json_encode(Response::sendError("Missing API Key"));
            exit;
        }

        // Validate API Key
        $apiKeyController = new APIKeyController();
        $isValid = $apiKeyController->isValidApiKey($apiKey);

        if (!$isValid['success']) {
            echo json_encode(Response::sendError("Invalid API Key"));
            exit;
        }

        // Store user ID for further use
        $this->userId = $isValid['data']['user_id'] ?? null;
    }
}
