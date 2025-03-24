<?php

namespace Argha\NetutilxApi\Controllers;

use Argha\NetutilxApi\Helpers\Response;
use Exception;
use Argha\NetutilxApi\Controllers\BaseApiController;
use Argha\NetutilxApi\Helpers\DnsHelper;
use Argha\NetutilxApi\Helpers\ServiceHelper;
use Argha\NetutilxApi\Helpers\ToolParamHelper;
use Argha\NetutilxApi\Helpers\ToolHistoryHelper;
use Argha\NetutilxApi\Config\Constants;

class DnsxController extends BaseApiController
{
    private $dnsHelper;
    private $toolsParamHelper;
    private $serviceHelper;
    private $toolHistoryHelper;

    public function __construct()
    {
        parent::__construct();
        Constants::init();
        $this->dnsHelper = new DnsHelper();
        $this->toolsParamHelper = new ToolParamHelper();
        $this->toolHistoryHelper = new ToolHistoryHelper();
        $this->serviceHelper = new ServiceHelper(Constants::$SERVICE_URL);
    }

    public function queryDns($query, $server, $type) {
        $isQuery = $this->dnsHelper->isDomainOrIp($query);
        if($isQuery === "unknown") {
            return Response::sendError("Invalid Query");
        }

        if($isQuery === "ip") {
            $response = $this->dnsHelper->parsePtrRecord($this->dnsHelper->ptrQuery($query));
            return $response;
        }
        return [];
    }

    public function queryDnsOld($query, $server, $type)
    {
        $isDomainOrIp = $this->dnsHelper->isDomainOrIp($query);

        if ($isDomainOrIp === "unknown") {
            return Response::sendError("Invalid query.");
        }

        // Prepare request parameters
        $params = [
            'query'  => $query,
            'server' => '8.8.8.8', // Default for IP lookup
        ];

        // Handle IP case separately (PTR record lookup)
        if ($isDomainOrIp === "ip") {
            $params['action'] = 'ip';
            $params['type']   = 'PTR';
        } else {
            // Validate type and server for domain lookups
            $typeValue = $this->toolsParamHelper->getToolParameterValue($type);
            $serverValue = $this->toolsParamHelper->getToolParameterValue($server);

            if (!$typeValue || !$serverValue) {
                return Response::sendError("Invalid type or server.");
            }

            $params['action'] = 'domain';
            $params['type']   = $typeValue;
            $params['server'] = $serverValue;
        }

        // Send request
        $response = $this->serviceHelper->sendGetRequest('dnsx', $params);

        // Ensure consistent success/failure response
        if ($response['success'] && isset($response['decoded_response'])) {
            // Store the API log
            $this->storeApiLog(
                $this->userId,
                $this->toolHistoryHelper->getUniqueRequestId(),
                'dnsx',
                Constants::$APP_URL,
                "/dnsx.php?query=$query&type=$type&server=$server&action=query",
                json_encode($this->dnsHelper->parseDnsRecord($response['decoded_response']['data']['output'])),
                $response['status_code']
            );

            return ["success" => true, "data" => $this->dnsHelper->parseDnsRecord($response['decoded_response']['data']['output'])];
        }

        return $response['response'] ?? null;
    }

    public function getHistory()
    {
        return $this->toolHistoryHelper->getHistory($this->userId, 'dnsx');
    }

    public function getDnsParameters($parameterType)
    {
        try {
            // Prepare the SQL statement using a parameter for parameter_type.
            $stmt = $this->connection->prepare("SELECT parameter_key, parameter_value, description FROM tool_parameters WHERE tool_name = 'dnsx' AND parameter_type = ? AND is_enabled = 1");
            if (!$stmt) {
                return Response::sendError("Prepare statement failed: " . $this->connection->error);
            }

            // Bind the provided parameter type.
            $stmt->bind_param("s", $parameterType);

            if (!$stmt->execute()) {
                return Response::sendError("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            if (!$result) {
                return Response::sendError("Getting result failed: " . $stmt->error);
            }

            $data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return Response::sendSuccess("DNS parameters fetched successfully.", $data);
        } catch (Exception $e) {
            error_log("Error in getDnsParameters: " . $e->getMessage());
            return Response::sendError("An unexpected error occurred: " . $e->getMessage());
        }
    }
}
