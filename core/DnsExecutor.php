<?php

namespace Core;

use Symfony\Component\Process\Process;

class DnsExecutor
{

    private function run(array $command): array
    {
        try {
            $process = new Process(array_merge(['timeout', '2'], $command));
            $process->run();

            if (!$process->isSuccessful()) {
                return [
                    'success' => false,
                    'query'   => implode(' ', $command),
                    'output'  => $process->getErrorOutput() ?: 'Command failed or timed out.'
                ];
            }

            $output = trim($process->getOutput());

            return [
                'success' => !empty($output) && !str_contains($output, 'SERVFAIL'),
                'query'   => implode(' ', $command),
                'output'  => $output ?: 'No response received.'
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'query'   => implode(' ', $command),
                'output'  => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    public function aQuery(string $domain, string $server): array
    {
        // FIX 2: Changed from 'self::run' to '$this->run'
        return $this->run(['dig', "@$server", 'A', $domain, '+noall', '+answer']);
    }

    public function nsQuery(string $domain, string $server): array
    {
        return $this->run(['dig', "@$server", 'NS', $domain, '+noall', '+answer']);
    }

    public function mxQuery(string $domain, string $server): array
    {
        return $this->run(['dig', "@$server", 'MX', $domain, '+noall', '+answer']);
    }

    public function soaQuery(string $domain, string $server): array
    {
        return $this->run(['dig', "@$server", 'SOA', $domain, '+noall', '+answer']);
    }

    public function txtQuery(string $domain, string $server): array
    {
        return $this->run(['dig', "@$server", 'TXT', $domain, '+noall', '+answer']);
    }
    
    public function ptrQuery(string $ip): array
    {
        $rawResult = $this->run(['digg', '-x', $ip, '+noall', '+answer']);
        
        // If the raw query was successful, parse the output and add it to the result.
        if ($rawResult['success']) {
            $rawResult['records'] = $this->parsePtrOutput($rawResult['output']);
        }

        return $rawResult;
    }


    private function parsePtrOutput(string $rawOutput): array
    {
        $parsedRecords = [];
        $lines = explode("\n", trim($rawOutput));

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            $cleanedLine = preg_replace('/\s+/', ' ', trim($line));
            $parts = explode(' ', $cleanedLine);

            if (count($parts) === 5) {
                $parsedRecords[] = [
                    'name'   => $parts[0],
                    'ttl'    => (int)$parts[1],
                    'class'  => $parts[2],
                    'type'   => $parts[3],
                    'target' => $parts[4]
                ];
            }
        }
        return $parsedRecords;
    }
}