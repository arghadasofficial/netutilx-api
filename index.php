<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ProcessDnsTester
{
    private function executeQuery(array $command)
    {
        $process = new Process(array_merge(['timeout', '2'], $command));
        $process->run();

        if (!$process->isSuccessful()) {
            return [
                "success" => false,
                "query"   => implode(' ', $command),
                "output"  => $process->getErrorOutput() ?: "Command execution failed or timed out."
            ];
        }

        $trimmedOutput = trim($process->getOutput());

        return [
            "success" => !empty($trimmedOutput) && !str_contains($trimmedOutput, "SERVFAIL"),
            "query"   => implode(' ', $command),
            "output"  => $trimmedOutput ?: "No response received."
        ];
    }

    public function aQuery($domain, $server)
    {
        return $this->executeQuery(['dig', "@$server", 'A', $domain, '+noall', '+answer']);
    }

    public function nsQuery($domain, $server)
    {
        return $this->executeQuery(['dig', "@$server", 'NS', $domain, '+noall', '+answer']);
    }
}

// Run test
$tester = new ProcessDnsTester();
header('Content-Type: application/json');
echo json_encode([
    'a'  => $tester->aQuery('google.com', '8.8.8.8'),
    'ns' => $tester->nsQuery('google.com', '8.8.8.8')
], JSON_PRETTY_PRINT);
