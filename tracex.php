<?php 
header('Content-Type: application/json');

use Argha\NetutilxApi\Helpers\Response;
use Argha\NetutilxApi\Controllers\TracexController;

require_once 'vendor/autoload.php';

$tracexController = new TracexController();

$response = $tracexController->traceRoute();

echo json_encode($response);
exit;

?>