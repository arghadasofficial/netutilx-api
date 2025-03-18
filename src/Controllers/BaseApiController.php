<?php

namespace Argha\NetutilxApi\Controllers;

use Argha\NetutilxApi\Helpers\Response;
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
        $isValid = $this->isValidApiKey(apiKey: $apiKey);

        if (!$isValid['success']) {
            echo json_encode(Response::sendError("Invalid API Key"));
            exit;
        }

        // Store user ID for further use
        $this->userId = $isValid['data']['user_id'] ?? null;
    }

    public function isValidApiKey($apiKey)
    {
        $stmt = $this->connection->prepare("SELECT user_id FROM api_keys WHERE api_key = ?");
        $stmt->bind_param("s", $apiKey);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result) {
            return Response::sendSuccess("API Key is valid.", $result);
        }
        return Response::sendError("API Key is invalid.");
    }

    public function storeApiLog($userId, $requestId, $logSource, $endpoint, $requestPayload, $responsePayload, $statusCode) {
        $stmt = $this->connection->prepare("INSERT INTO `tool_history`(`user_id`, `request_id`, `log_source`, `endpoint`, `request_payload`, `response_payload`, `status_code`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param('isssssi', $userId, $requestId, $logSource, $endpoint, $requestPayload, $responsePayload, $statusCode); 

        $stmt->execute();

        $stmt->close();
    }
    
}
