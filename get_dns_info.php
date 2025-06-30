<?php
require_once __DIR__ . '../vendor/autoload.php';

// Add these lines at the very top of your script
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Core\ProcessRunner;
use Utils\ResponseHelper;
use Config\Database;
use Core\DnsExecutor;
use Services\DnsServerService;
use Services\DnsTypeService;
use Services\DnsInfoService;

$database = new Database();
$db = $database->connect();

$dnsServerService = new DnsServerService($db);
$dnsTypeService = new DnsTypeService($db);

$processRunner = new ProcessRunner();
$dnsExecutor = new DnsExecutor($processRunner);

$dnsInfoService = new DnsInfoService($dnsServerService, $dnsTypeService, $dnsExecutor);

$query = $_GET['query'] ?? null;
$serverId = isset($_GET['serverId']) ? (int)$_GET['serverId'] : null;
$typeId = isset($_GET['typeId']) ? (int)$_GET['typeId'] : null;

if (!$query || $serverId === null || $typeId === null) {
    ResponseHelper::error(
        'Missing required parameters. Please provide "query", "serverId", and "typeId".',
        400
    );
}

$result = $dnsInfoService->queryDnsInfo($query, $serverId, $typeId);

if ($result['success']) {
    ResponseHelper::success($result['records'], 'Dns Info Fetched Successfully');
} else {
    ResponseHelper::error(
        $result['output'],
        422
    );
}
