<?php 
namespace Argha\NetutilxApi\Helpers;

use Argha\NetutilxApi\Config\Database;
use Exception;

class ToolParamHelper {
    private $connection;

    public function __construct() {
        $this->connection = Database::getInstance()->getConnection();
    }

    public function getToolParameterValue($parameterKey)
    {
        try {
            $stmt = $this->connection->prepare("SELECT parameter_value FROM tool_parameters WHERE parameter_key = ?");
            $stmt->bind_param("s", $parameterKey);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $result['parameter_value'] ?? null;
        } catch (Exception $e) {
            return null;
        }
    }
}

?>