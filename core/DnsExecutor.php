<?php

namespace Core;

use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class DnsExecutor
{

    private function run(array $command): array
    {
        try {
            $process = new Process($command);
            $process->setTimeout(2);
            $process->run();

            if (!$process->isSuccessful()) {
                return [
                    'success' => false,
                    'query'   => implode(' ', $command),
                    'output'  => $process->getErrorOutput() ?: 'Command failed with no error output.'
                ];
            }

            $output = trim($process->getOutput());
            $isSuccess = !empty($output) && !str_contains($output, 'SERVFAIL');

            return [
                'success' => $isSuccess,
                'query'   => implode(' ', $command),
                'output'  => $isSuccess ? $output : ($output ?: 'No response received.')
            ];
        } catch (ProcessTimedOutException $e) {
            return [
                'success' => false,
                'query'   => implode(' ', $command),
                'output'  => 'Error: The command timed out after 2 seconds.'
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'query'   => implode(' ', $command),
                'output'  => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    // --- Private Parsing Helpers ---

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
    
    // The parsePtrOutput is just an alias for the simple parser
    private function parsePtrOutput(string $rawOutput): array
    {
        return $this->parseSimpleRecord($rawOutput, 'target');
    }

    // --- Public Query Methods (All now use their respective parsers) ---

    public function aQuery(string $domain, string $server): array
    {
        $rawResult = $this->run(['dig', "@$server", 'A', $domain, '+noall', '+answer']);
        if ($rawResult['success']) {
            $rawResult['records'] = $this->parseSimpleRecord($rawResult['output'], 'address');
        }
        return $rawResult;
    }

    public function nsQuery(string $domain, string $server): array
    {
        $rawResult = $this->run(['dig', "@$server", 'NS', $domain, '+noall', '+answer']);
        if ($rawResult['success']) {
            $rawResult['records'] = $this->parseSimpleRecord($rawResult['output'], 'target');
        }
        return $rawResult;
    }

    public function mxQuery(string $domain, string $server): array
    {
        $rawResult = $this->run(['dig', "@$server", 'MX', $domain, '+noall', '+answer']);
        if ($rawResult['success']) {
            $rawResult['records'] = $this->parseMxRecord($rawResult['output']);
        }
        return $rawResult;
    }

    public function soaQuery(string $domain, string $server): array
    {
        $rawResult = $this->run(['dig', "@$server", 'SOA', $domain, '+noall', '+answer']);
        if ($rawResult['success']) {
            $rawResult['records'] = $this->parseSoaRecord($rawResult['output']);
        }
        return $rawResult;
    }

    public function txtQuery(string $domain, string $server): array
    {
        $rawResult = $this->run(['dig', "@$server", 'TXT', $domain, '+noall', '+answer']);
        if ($rawResult['success']) {
            $rawResult['records'] = $this->parseTxtRecord($rawResult['output']);
        }
        return $rawResult;
    }
    
    public function ptrQuery(string $ip): array
    {
        $rawResult = $this->run(['dig', '-x', $ip, '+noall', '+answer']);
        if ($rawResult['success']) {
            $rawResult['records'] = $this->parsePtrOutput($rawResult['output']);
        }
        return $rawResult;
    }
}