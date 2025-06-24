<?php
// We no longer set headers here, assuming a front controller handles it.

// 1. Load all your classes
require_once __DIR__ . '/vendor/autoload.php';

// 2. Add 'use' statements
use Utils\ResponseHelper;
use Config\Database;
use Core\DnsExecutor;
use Services\DnsServerService;
use Services\DnsTypeService;
use Services\DnsInfoService;

// --- SETUP ---

// 3. Create the database connection
$database = new Database();
$db = $database->connect();

// 4. Instantiate the dependencies
$dnsServerService = new DnsServerService($db);
$dnsTypeService = new DnsTypeService($db);
$dnsExecutor = new DnsExecutor();

// 5. Create the DnsInfoService
$dnsInfoService = new DnsInfoService($dnsServerService, $dnsTypeService, $dnsExecutor);


// --- EXECUTION ---

// 6. Get input parameters
$query = $_GET['query'] ?? null;
$serverId = isset($_GET['serverId']) ? (int)$_GET['serverId'] : null;
$typeId = isset($_GET['typeId']) ? (int)$_GET['typeId'] : null;

// 7. Validate input
if (!$query || $serverId === null || $typeId === null) {
    ResponseHelper::error(
        'Missing required parameters. Please provide "query", "serverId", and "typeId".',
        400
    );
}

// 8. Use the service to perform the query
$result = $dnsInfoService->queryDnsInfo($query, $serverId, $typeId);

// 9. Send the result back to the user
if ($result['success']) {
    ResponseHelper::success($result);
} else {
    ResponseHelper::error(
        $result['output'],
        422
    );
}
