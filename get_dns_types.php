<?php
require_once __DIR__ . '../vendor/autoload.php';

use Services\DnsTypeService;
use Utils\ResponseHelper;
use Config\Database;

$database = new Database();
$dbConnection = $database->connect();

$dnsTypeService = new DnsTypeService($dbConnection);

try {
    $dnsTypes = $dnsTypeService->getAllDnsTypes();
    if ($dnsTypes) {
        ResponseHelper::success($dnsTypes, 'DNS types retrieved successfully.');
    } else {
        ResponseHelper::error('No DNS types found.', 404);
    }
} catch (Exception $e) {
    ResponseHelper::error('Unable to retrieve DNS types.', $e->getMessage());
}