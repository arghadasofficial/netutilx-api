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
    default:
        $response = Response::sendError("Invalid parameter");
        break;
}

echo json_encode($response);
exit;
