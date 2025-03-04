<?php
header('Content-Type: application/json');

use Argha\NetutilxApi\Controllers\DnsxController;

require_once 'vendor/autoload.php';

$dnsxController = new DnsxController();
$response = $dnsxController->getDnsServers();
echo json_encode($response);