<?php

namespace Argha\NetutilxApi\Controllers;

use Argha\NetutilxApi\Helpers\Response;
use Argha\NetutilxApi\Controllers\APIKeyController;
use Exception;
use Argha\NetutilxApi\Config\Database;

class BaseApiController {

    // fixed the error in the code
    protected $userId = null;
    protected $connection;

    public function __construct() {
        // Extract API Key from headers
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $apiKey = $headers['X-API-KEY'] ?? ($_SERVER['HTTP_X_API_KEY'] ?? null);

        if (!$apiKey) {
            echo json_encode(Response::sendError("Missing API Key"));
            exit;
        }

        $this->connection = Database::getInstance()->getConnection();

        // Validate API Key
        $apiKeyController = new APIKeyController();
        $isValid = $apiKeyController->isValidApiKey(apiKey: $apiKey);

        if (!$isValid['success']) {
            echo json_encode(Response::sendError("Invalid API Key"));
            exit;
        }

        // Store user ID for further use
        $this->userId = $isValid['data']['user_id'] ?? null;
    }

    public function storeApiLog($userId, $requestId, $logSource, $endpoint, $requestPayload, $responsePayload, $statusCode) {
        $stmt = $this->connection->prepare("INSERT INTO `tool_history`(`user_id`, `request_id`, `log_source`, `endpoint`, `request_payload`, `response_payload`, `status_code`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param('isssssi', $userId, $requestId, $logSource, $endpoint, $requestPayload, $responsePayload, $statusCode); 

        $stmt->execute();

        $stmt->close();
    }
    
}
