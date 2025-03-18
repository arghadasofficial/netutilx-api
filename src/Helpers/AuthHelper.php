<?php
namespace Argha\NetutilxApi\Helpers;

use Argha\NetutilxApi\Config\Database;

class AuthHelper {

    private $connection;

    public function __construct() {
        $this->connection = Database::getInstance()->getConnection();
    }

    public function isUserExist($email) {
        return true;
    }

    public function isPasswordMatched($email, $password) {
        return true;
    }

    public function getUserId($email) {
        return 1;
    }

    public function getApiKey($userId) {
        
    }
}

?>