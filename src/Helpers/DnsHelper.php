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

    private function executeQuery($command)
    {
        $escapedCommand = escapeshellcmd($command);
        $output = shell_exec("timeout 2 $escapedCommand 2>&1");

        if ($output === null) {
            return [
                "success" => false,
                "query"   => $command,
                "output"  => "Command execution failed or timed out."
            ];
        }

        $trimmedOutput = trim((string)$output);

        return [
            "success" => !empty($trimmedOutput) && !str_contains($trimmedOutput, "SERVFAIL"),
            "query"   => $command,
            "output"  => $trimmedOutput ?: "No response received."
        ];
    }

    function aQuery($domain, $server)
    {
        return executeQuery("dig @$server A $domain +noall +answer");
    }

    function nsQuery($domain, $server)
    {
        return executeQuery("dig @$server NS $domain +noall +answer");
    }

    function mxQuery($domain, $server)
    {
        return executeQuery("dig @$server MX $domain +noall +answer");
    }

    function soaQuery($domain, $server)
    {
        return executeQuery("dig @$server SOA $domain +noall +answer");
    }

    function txtQuery($domain, $server)
    {
        return executeQuery("dig @$server TXT $domain +noall +answer");
    }

    function ptrQuery($ip)
    {
        return executeQuery("dig -x $ip +noall +answer");
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
