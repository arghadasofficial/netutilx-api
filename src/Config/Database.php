<?php

namespace Argha\NetutilxApi\Config;

use Dotenv\Dotenv;

/**
 * Class Database
 * 
 * Handles database connection using the Singleton pattern to ensure only one
 * connection instance exists throughout the application lifecycle.
 */
class Database
{
    // Database connection properties
    private $host;
    private $user;
    private $password;
    private $database;
    private $connection;

    // Holds the singleton instance of the Database class
    private static $instance = null;

    /**
     * Database constructor.
     * 
     * Loads environment variables and establishes the database connection.
     * The constructor is made private/protected implicitly (by using Singleton)
     * to prevent multiple instances.
     */
    public function __construct()
    {
        // Load environment variables from the .env file
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        // Set database credentials from environment variables
        $this->host = $_ENV['DB_HOST'];
        $this->user = $_ENV['DB_USERNAME'];
        $this->password = $_ENV['DB_PASSWORD'];
        $this->database = $_ENV['DB_DATABASE'];

        // Create the database connection
        $this->connection = mysqli_connect($this->host, $this->user, $this->password, $this->database);

        // Check for connection errors
        if ($this->connection->connect_error) {
            die("Database Connection failed: " . $this->connection->connect_error);
        }
    }

    /**
     * Get the singleton instance of the Database class.
     * 
     * Ensures only one instance of the Database exists.
     * 
     * @return Database The single instance of the Database class.
     */
    public static function getInstance(): Database
    {
        // Create a new instance only if it doesn't exist
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the active database connection.
     * 
     * @return \mysqli The active MySQLi connection instance.
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Close the active database connection.
     * 
     * Frees the database connection when it's no longer needed.
     */
    public function closeConnection()
    {
        $this->connection->close();
    }
}
