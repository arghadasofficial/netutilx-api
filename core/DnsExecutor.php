<?php

namespace Core;

class DnsExecutor
{
    private ProcessRunner $processRunner;

    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    private function parseSimpleRecord(string $rawOutput, string $dataKey = 'target'): array
    {
        $records = [];
        $lines = explode("\n", trim($rawOutput));
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            $parts = preg_split('/\s+/', trim($line));
            if (count($parts) === 5) {
                $records[] = [
                    'name'    => $parts[0],
                    'ttl'     => (int)$parts[1],
                    'class'   => $parts[2],
                    'type'    => $parts[3],
                    $dataKey  => $parts[4]
                ];
            }
        }
        return $records;
    }

    private function parseMxRecord(string $rawOutput): array
    {
        $records = [];
        $lines = explode("\n", trim($rawOutput));
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            $parts = preg_split('/\s+/', trim($line));
            if (count($parts) === 6) {
                $records[] = [
                    'name'     => $parts[0],
                    'ttl'      => (int)$parts[1],
                    'class'    => $parts[2],
                    'type'     => $parts[3],
                    'priority' => (int)$parts[4],
                    'target'   => $parts[5]
                ];
            }
        }
        return $records;
    }

    private function parseSoaRecord(string $rawOutput): array
    {
        $records = [];
        $lines = explode("\n", trim($rawOutput));
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            $parts = preg_split('/\s+/', trim($line));
            if (count($parts) >= 11) {
                $records[] = [
                    'name'    => $parts[0],
                    'ttl'     => (int)$parts[1],
                    'class'   => $parts[2],
                    'type'    => $parts[3],
                    'mname'   => $parts[4],
                    'rname'   => $parts[5],
                    'serial'  => $parts[6],
                    'refresh' => (int)$parts[7],
                    'retry'   => (int)$parts[8],
                    'expire'  => (int)$parts[9],
                    'minimum' => (int)$parts[10]
                ];
            }
        }
        return $records;
    }

    private function parseTxtRecord(string $rawOutput): array
    {
        $records = [];
        $lines = explode("\n", trim($rawOutput));
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            preg_match('/^(\S+)\s+(\d+)\s+(\S+)\s+(\S+)\s+(.*)$/', $line, $matches);
            if (count($matches) === 6) {
                $records[] = [
                    'name'    => $matches[1],
                    'ttl'     => (int)$matches[2],
                    'class'   => $matches[3],
                    'type'    => $matches[4],
                    'content' => trim($matches[5], '"')
                ];
            }
        }
        return $records;
    }

    public function aQuery(string $domain, string $server): array
    {
        $command = ['dig', "@$server", 'A', $domain, '+noall', '+answer'];
        $rawResult = $this->processRunner->execute($command);
        if ($rawResult['success'] && !str_contains($rawResult['output'], 'SERVFAIL')) {
            $rawResult['records'] = $this->parseSimpleRecord($rawResult['output'], 'address');
        } else {
            $rawResult['success'] = false;
        }
        return $rawResult;
    }

    public function nsQuery(string $domain, string $server): array
    {
        $command = ['dig', "@$server", 'NS', $domain, '+noall', '+answer'];
        $rawResult = $this->processRunner->execute($command);
        if ($rawResult['success'] && !str_contains($rawResult['output'], 'SERVFAIL')) {
            $rawResult['records'] = $this->parseSimpleRecord($rawResult['output'], 'target');
        } else {
            $rawResult['success'] = false;
        }
        return $rawResult;
    }

    public function mxQuery(string $domain, string $server): array
    {
        $command = ['dig', "@$server", 'MX', $domain, '+noall', '+answer'];
        $rawResult = $this->processRunner->execute($command);
        if ($rawResult['success'] && !str_contains($rawResult['output'], 'SERVFAIL')) {
            $rawResult['records'] = $this->parseMxRecord($rawResult['output']);
        } else {
            $rawResult['success'] = false;
        }
        return $rawResult;
    }

    public function soaQuery(string $domain, string $server): array
    {
        $command = ['dig', "@$server", 'SOA', $domain, '+noall', '+answer'];
        $rawResult = $this->processRunner->execute($command);
        if ($rawResult['success'] && !str_contains($rawResult['output'], 'SERVFAIL')) {
            $rawResult['records'] = $this->parseSoaRecord($rawResult['output']);
        } else {
            $rawResult['success'] = false;
        }
        return $rawResult;
    }

    public function txtQuery(string $domain, string $server): array
    {
        $command = ['dig', "@$server", 'TXT', $domain, '+noall', '+answer'];
        $rawResult = $this->processRunner->execute($command);
        if ($rawResult['success'] && !str_contains($rawResult['output'], 'SERVFAIL')) {
            $rawResult['records'] = $this->parseTxtRecord($rawResult['output']);
        } else {
            $rawResult['success'] = false;
        }
        return $rawResult;
    }

    public function ptrQuery(string $ip): array
    {
        $command = ['dig', '-x', $ip, '+noall', '+answer'];
        $rawResult = $this->processRunner->execute($command);
        if ($rawResult['success'] && !str_contains($rawResult['output'], 'SERVFAIL')) {
            $rawResult['records'] = $this->parseSimpleRecord($rawResult['output'], 'target');
        } else {
            $rawResult['success'] = false;
        }
        return $rawResult;
    }
}
