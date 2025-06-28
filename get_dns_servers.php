<?php
require_once __DIR__ . '../vendor/autoload.php';

use Utils\ResponseHelper;
use Config\Database;
use Services\DnsServerService;

$database = new Database();
$dbConnection = $database->connect();

$dnsServerService = new DnsServerService($dbConnection);

try {
    $dnsServers = $dnsServerService->getAllDnsServers();
    if($dnsServers) {
        ResponseHelper::success($dnsServers, 'DNS servers retrieved successfully.');
    } else {
        ResponseHelper::error('No DNS servers found.', 404);
    }
} catch (Exception $e) {
    ResponseHelper::error('Unable to retrieve DNS servers.', $e->getMessage());
}
