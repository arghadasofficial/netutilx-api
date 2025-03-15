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

    function parseDnsRecord($output)
    {
        // Split output by lines and remove empty lines
        $lines = array_filter(explode("\n", $output));
    
        $results = array();
        foreach ($lines as $line) {
            // Split each line by whitespace
            $parts = preg_split('/\s+/', $line);
    
            // Extract name, TTL, class, type, and data
            $name = $parts[0];
            $ttl = $parts[1];
            $class = $parts[2];
            $type = $parts[3];
    
            // Special handling for SOA records
            if ($type === 'SOA') {
                $data = array(
                    'primary_nameserver' => $parts[4],
                    'responsible_authority' => $parts[5],
                    'serial' => $parts[6],
                    'refresh' => $parts[7],
                    'retry' => $parts[8],
                    'expire' => $parts[9],
                    'min_ttl' => $parts[10]
                );
            } else {
                $data = implode(' ', array_slice($parts, 4));
            }
    
            // Add parsed data to results array
            $results[] = array(
                'name' => $name,
                'ttl' => $ttl,
                'class' => $class,
                'type' => $type,
                'data' => $data
            );
        }
    
        return $results;
    }
    
    function parsePtrRecord($ptrRecord)
    {
        // Split the PTR record by whitespace
        $parts = preg_split('/\s+/', $ptrRecord);
    
        // Extract the relevant information
        $name = $parts[0];
        $ttl = $parts[1];
        $type = $parts[2];
        $class = $parts[3];
        $data = $parts[4];
    
        // Return the parsed PTR record data
        return array(
            'name' => $name,
            'ttl' => $ttl,
            'type' => $type,
            'class' => $class,
            'data' => $data
        );
    }
}
