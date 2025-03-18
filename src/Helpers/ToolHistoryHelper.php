<?php

namespace Argha\NetutilxApi\Helpers;

use Argha\NetutilxApi\Config\Database;
use Exception;

class ToolHistoryHelper
{

    private $connection;

    public function __construct()
    {
        $this->connection = Database::getInstance()->getConnection();
    }


    public function getHistory($user_id, $tool_source)
    {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM `tool_history` WHERE user_id = ? AND log_source = ? ORDER BY created_at DESC");
            $stmt->bind_param("is", $user_id, $tool_source);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return Response::sendSuccess("Request history fetched successfully.", $result);
        } catch (Exception $e) {
            return Response::sendError("An unexcepted error occured: " . $e->getMessage());
        }
    }
    public function getUniqueRequestId()
    {
        do {
            $uuid = $this->generateRequestId();
            $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM `tool_history` WHERE `request_id` = ?");
            $stmt->bind_param("s", $uuid);
            $stmt->execute();

            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
        } while ($count > 0);

        return $uuid;
    }

    private function generateRequestId()
    {
        // Define the characters to choose from.
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $group1 = '';
        $group2 = '';
        // Generate two groups of 3 characters each.
        for ($i = 0; $i < 3; $i++) {
            $group1 .= $chars[random_int(0, strlen($chars) - 1)];
            $group2 .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return 'req-' . $group1 . '-' . $group2;
    }
}
