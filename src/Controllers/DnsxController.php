<?php

namespace Argha\NetutilxApi\Controllers;

use Argha\NetutilxApi\Config\Database;
use Argha\NetutilxApi\Helpers\Response;

class DnsxController
{
    private $connection;

    public function __construct()
    {
        $this->connection = Database::getInstance()->getConnection();
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
        } catch (\Exception $e) {
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

            return Response::sendSuccess("DNS types fetched successfully.", $data);
        } catch (\Exception $e) {
            error_log("Error in getDnsTypes: " . $e->getMessage());
            return Response::sendError("An unexpected error occurred: " . $e->getMessage());
        }
    }
}
