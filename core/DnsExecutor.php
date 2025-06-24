<?php
namespace Core;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DnsExecutor
{
    private static function run(array $command): array
    {
        try {
            // Prepend timeout to avoid hanging
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

    public static function aQuery(string $domain, string $server): array
    {
        return self::run(['dig', "@$server", 'A', $domain, '+noall', '+answer']);
    }

    public static function nsQuery(string $domain, string $server): array
    {
        return self::run(['dig', "@$server", 'NS', $domain, '+noall', '+answer']);
    }

    public static function mxQuery(string $domain, string $server): array
    {
        return self::run(['dig', "@$server", 'MX', $domain, '+noall', '+answer']);
    }

    public static function soaQuery(string $domain, string $server): array
    {
        return self::run(['dig', "@$server", 'SOA', $domain, '+noall', '+answer']);
    }

    public static function txtQuery(string $domain, string $server): array
    {
        return self::run(['dig', "@$server", 'TXT', $domain, '+noall', '+answer']);
    }

    public static function ptrQuery(string $ip): array
    {
        return self::run(['dig', '-x', $ip, '+noall', '+answer']);
    }
}
