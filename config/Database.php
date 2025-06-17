<?php
namespace Config;

use Dotenv\Dotenv;
use mysqli;

class Database
{
    public static function connect(): mysqli
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $user = $_ENV['DB_USER'] ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? '';
        $db   = $_ENV['DB_NAME'] ?? 'netutilx';

        $conn = new mysqli($host, $user, $pass, $db);

        if ($conn->connect_error) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database connection failed',
                'error'   => $conn->connect_error
            ]);
            exit;
        }

        return $conn;
    }
}
