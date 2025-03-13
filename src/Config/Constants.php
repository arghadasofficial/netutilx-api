<?php

namespace Argha\NetutilxApi\Config;

use Dotenv\Dotenv;

class Constants
{
    public static $APP_NAME;
    public static $APP_ENV;
    public static $APP_DEBUG;
    public static $APP_URL;
    public static $SERVICE_URL;

    // A flag to ensure that the environment is loaded only once
    private static $initialized = false;

    /**
     * Initializes the Constants by loading environment variables from the .env file.
     *
     * This method should be called once at the start of your application.
     */
    public static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        // Load environment variables from the .env file located two directories up.
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        // Set the constants from the environment variables.
        self::$APP_NAME = $_ENV['APP_NAME'] ?? null;
        self::$APP_ENV = $_ENV['APP_ENV'] ?? null;
        self::$APP_DEBUG = $_ENV['APP_DEBUG'] ?? null;
        self::$APP_URL = $_ENV['APP_URL'] ?? null;
        self::$SERVICE_URL = $_ENV['SERVICE_URL'] ?? null;

        self::$initialized = true;
    }
}
