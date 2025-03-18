<?php 
header('Content-Type: application/json');

use Argha\NetutilxApi\Controllers\AuthController;
use Argha\NetutilxApi\Helpers\Response;

require_once 'vendor/autoload.php';

$authController = new AuthController();

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if ($email && $password) {
    $response = $authController->login($email, $password);
} else {
    $response = Response::sendError("Empty parameter values");
}

echo json_encode($response);
exit;

?>