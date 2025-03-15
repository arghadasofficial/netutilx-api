<?php 
header('Content-Type: application/json');

use Argha\NetutilxApi\Helpers\Response;
use Argha\NetutilxApi\Controllers\PingxController;

require_once 'vendor/autoload.php';

$pingxController = new PingxController();

$response = $pingxController->queryPing();

echo json_encode($response);
exit;

?>