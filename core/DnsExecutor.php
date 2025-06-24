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
            // Set the timeout directly on the process object for clarity
            $process->setTimeout(2);
            $process->run();

            if (!$process->isSuccessful()) {
                // This will now be caught by our specific exception handling below
                // but we keep it as a fallback.
                return [
                    'success' => false,
                    'query'   => implode(' ', $command),
                    'output'  => $process->getErrorOutput() ?: 'Command failed with no error output.'
                ];
            }

            $output = trim($process->getOutput());

            // This success check is good. It handles empty responses and server failures.
            $isSuccess = !empty($output) && !str_contains($output, 'SERVFAIL');

            return [
                'success' => $isSuccess,
                'query'   => implode(' ', $command),
                'output'  => $isSuccess ? $output : ($output ?: 'No response received.')
            ];
        } catch (ProcessTimedOutException $e) {
            // Specifically catch a timeout exception
            return [
                'success' => false,
                'query'   => implode(' ', $command),
                'output'  => 'Error: The command timed out after 2 seconds.'
            ];
        } catch (\Throwable $e) {
            // Catch any other general exception
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
        $rawResult = $this->run(['dig', '-x', $ip, '+noall', '+answer']);

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
