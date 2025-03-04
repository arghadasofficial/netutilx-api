<?php 
namespace Argha\NetutilxApi\Helpers;

use Argha\NetutilxApi\Config\Database;

class Token {

    private $connection;

    public function __construct() {
        $this->connection = Database::getInstance()->getConnection();
    }

    

}

?>