<?php

namespace Argha\NetutilxApi\Controllers;

use Argha\NetutilxApi\Config\Database;
use Argha\NetutilxApi\Helpers\Response;
use Exception;

class DnsxController extends BaseAPIController
{
    private $connection;

    public function __construct()
    {   
        parent::__construct();
        $this->connection = Database::getInstance()->getConnection();
    }       

    public function getRequestHistory() {
        try {
            $stmt = $this->connection->prepare("SELECT id, request_id, request_data, status_code, created_at FROM api_logs WHERE user_id = ? AND log_type = 'API_REQUEST'");
            $stmt->bind_param("i", $this->userId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return Response::sendSuccess("Request history fetched successfully.", $result);
        } catch(Exception $e) {
            return Response::sendError("An unexcepted error occured: " . $e->getMessage());
        }
    }

    public function getDetailedHistory($requestId) {
        try {
            $stmt = $this->connection->prepare("SELECT id, log_type, request_data, response_data, status_code, created_at FROM api_logs WHERE request_id = ?");
            $stmt->bind_param("s", $requestId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return Response::sendSuccess("Detailed History fetched successfully.", $result);
        } catch(Exception $e) {
            return Response::sendError("An unexcepted error occured: " . $e->getMessage());
        }
    }

    public function getDnsTypes()
    {
        try {
            $stmt = $this->connection->prepare("SELECT parameter_key, parameter_value, description FROM tool_parameters WHERE tool_name = 'dnsx' AND parameter_type = 'dns_type' AND is_enabled = 1");
            if (!$stmt) {
                return Response::sendError("Prepare statement failed: " . $this->connection->error);
            }

            if (!$stmt->execute()) {
                return Response::sendError("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            if (!$result) {
                return Response::sendError("Getting result failed: " . $stmt->error);
            }

            $data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return Response::sendSuccess("DNS types fetched successfully.", $data);
        } catch (Exception $e) {
            error_log("Error in getDnsTypes: " . $e->getMessage());
            return Response::sendError("An unexpected error occurred: " . $e->getMessage());
        }
    }

    public function getDnsServers()
    {
        try {
            $stmt = $this->connection->prepare("SELECT parameter_key, parameter_value, description FROM tool_parameters WHERE tool_name = 'dnsx' AND parameter_type = 'dns_provider' AND is_enabled = 1");
            if (!$stmt) {
                return Response::sendError("Prepare statement failed: " . $this->connection->error);
            }

            if (!$stmt->execute()) {
                return Response::sendError("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            if (!$result) {
                return Response::sendError("Getting result failed: " . $stmt->error);
            }

            $data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return Response::sendSuccess("DNS servers fetched successfully.", $data);
        } catch (Exception $e) {
            error_log("Error in getDnsServers: " . $e->getMessage());
            return Response::sendError("An unexpected error occurred: " . $e->getMessage());
        }
    }
}
