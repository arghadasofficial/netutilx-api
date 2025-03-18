<?php 
namespace Argha\NetutilxApi\Controllers;

use Argha\NetutilxApi\Config\Database;
use Argha\NetutilxApi\Helpers\AuthHelper;
use Argha\NetutilxApi\Helpers\Response;

class AuthController {
    private $connection;
    private $authHelper;

    public function __construct() {
        $this->connection = Database::getInstance()->getConnection();    
        $this->authHelper = new AuthHelper();
    }

    public function login($email, $password) {
        if(!$this->authHelper->isUserExist($email)) {
            return Response::sendError("Invalid Credential");
        }

        if (!$this->authHelper->isPasswordMatched($email, $password)) {
            return Response::sendError("Incorrect Password");
        }
        
        $userId = $this->authHelper->getUserId($email);
        $apiKey = $this->authHelper->getApiKey($userId);

        return Response::sendSuccess("Login Successful", ["apiKey" => $apiKey]);
        
    }

    public function register() {

    }
}

?>