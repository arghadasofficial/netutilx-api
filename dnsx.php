<?php
header('Content-Type: application/json');

use Argha\NetutilxApi\Controllers\DnsxController;
use Argha\NetutilxApi\Helpers\Response;

require_once 'vendor/autoload.php';

$dnsxController = new DnsxController();

$action = $_GET['action'] ?? null;

switch ($action) {
    case 'types':
        $response = $dnsxController->getDnsTypes();
        break;
    case 'servers':
        $response = $dnsxController->getDnsServers();
        break;
    case 'history':
        $requestId = $_GET['request_id'] ?? null;
        if ($requestId) {
            $response = $dnsxController->getDetailedHistory($requestId);
        } else {
            $response = $dnsxController->getRequestHistory();
        }
        break;
    case 'query':
        $query = $_GET['query'] ?? null;
        $type = $_GET['type'] ?? null;
        $server = $_GET['server'] ?? null;
        
        if ($query && $type && $server) {
            $response = $dnsxController->queryDns($query, $server, $type);
        } else {
            $response = Response::sendError("Empty parameter values");
        }
        break;
    default:
        $response = Response::sendError("Invalid parameter");
        break;
}

echo json_encode($response);
exit;
