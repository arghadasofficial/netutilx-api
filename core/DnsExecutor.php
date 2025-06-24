<?php

namespace Core;

class DnsExecutor
{
    private ProcessRunner $processRunner;

    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * The single, unified parser that handles all record types.
     */
    private function parseOutput(string $rawOutput): array
    {
        $records = [];
        $lines = explode("\n", trim($rawOutput));

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            $line = trim($line);
            // This regex reliably captures the first 4 parts, and the rest of the line.
            preg_match('/^(\S+)\s+(\d+)\s+(\S+)\s+(\S+)\s+(.*)$/', $line, $matches);

            if (count($matches) < 5) continue; // Skip malformed lines

            $type = $matches[4];
            $dataString = $matches[5];
            $data = null;

            switch ($type) {
                case 'SOA':
                    $parts = preg_split('/\s+/', $dataString);
                    $data = [
                        'mname'   => $parts[0],
                        'rname'   => $parts[1],
                        'serial'  => $parts[2],
                        'refresh' => (int)$parts[3],
                        'retry'   => (int)$parts[4],
                        'expire'  => (int)$parts[5],
                        'minimum' => (int)$parts[6]
                    ];
                    break;
                case 'TXT':
                    $data = trim($dataString, '"');
                    break;

                case 'A':
                case 'NS':
                case 'CNAME':
                case 'PTR':
                case 'MX':
                default:
                    $data = $dataString;
                    break;
            }

            $records[] = [
                'name'  => $matches[1],
                'ttl'   => (int)$matches[2],
                'class' => $matches[3],
                'type'  => $type,
                'data'  => $data
            ];
        }

        return $records;
    }

    private function executeAndParse(array $command): array
    {
        $rawResult = $this->processRunner->execute($command);
        if ($rawResult['success'] && !str_contains($rawResult['output'], 'SERVFAIL')) {
            $rawResult['records'] = $this->parseOutput($rawResult['output']);
        } else {
            $rawResult['success'] = false;
        }
        return $rawResult;
    }

    public function aQuery(string $domain, string $server): array
    {
        return $this->executeAndParse(['dig', "@$server", 'A', $domain, '+noall', '+answer']);
    }

    public function nsQuery(string $domain, string $server): array
    {
        return $this->executeAndParse(['dig', "@$server", 'NS', $domain, '+noall', '+answer']);
    }

    public function mxQuery(string $domain, string $server): array
    {
        return $this->executeAndParse(['dig', "@$server", 'MX', $domain, '+noall', '+answer']);
    }

    public function soaQuery(string $domain, string $server): array
    {
        return $this->executeAndParse(['dig', "@$server", 'SOA', $domain, '+noall', '+answer']);
    }

    public function txtQuery(string $domain, string $server): array
    {
        return $this->executeAndParse(['dig', "@$server", 'TXT', $domain, '+noall', '+answer']);
    }

    public function ptrQuery(string $ip): array
    {
        return $this->executeAndParse(['dig', '-x', $ip, '+noall', '+answer']);
    }
}
