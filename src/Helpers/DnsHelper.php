<?php

namespace Argha\NetutilxApi\Helpers;

use Argha\NetutilxApi\Config\Database;
use Exception;

class DnsHelper
{
    private $connection;

    public function __construct()
    {
        $this->connection = Database::getInstance()->getConnection();
    }

    public function isDomainOrIp($query)
    {
        // Regular expression pattern for domain name validation
        $pattern = '/^(?:(?:[a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)+[a-zA-Z]{2,}$/';

        // Check if the input matches the domain name pattern
        if (preg_match($pattern, $query)) {
            return "domain";
        } elseif (filter_var($query, FILTER_VALIDATE_IP)) {
            return "ip";
        }
        return "unknown";
    }

    public function parseDnsQuery() {}

    public function parsePtrQuery() {}
}
