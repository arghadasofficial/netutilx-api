<?php

namespace Argha\NetutilxApi\Controllers;

use Argha\NetutilxApi\Config\Database;
use Argha\NetutilxApi\Helpers\Response;
use Exception;

class APIKeyController
{
    private $connection;

    public function __construct()
    {
        $this->connection = Database::getInstance()->getConnection();
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
}
