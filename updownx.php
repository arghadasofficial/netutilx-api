<?php 
header('Content-Type: application/json');

use Argha\NetutilxApi\Controllers\UpDownxController;
use Argha\NetutilxApi\Helpers\Response;

require_once 'vendor/autoload.php';

$updownxController = new UpDownxController();

$query = $_GET['query'] ?? null;

if ($query) {
    $response = $updownxController->fetchUpDown($query);
} else {
    $response = Response::sendError("Empty parameter values");
}

echo json_encode($response);
exit;

?>