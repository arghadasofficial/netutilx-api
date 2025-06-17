<?php
class ShellDnsTester
{
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

    public function aQuery($domain, $server)
    {
        return $this->executeQuery("dig @$server A $domain +noall +answer");
    }

    public function nsQuery($domain, $server)
    {
        return $this->executeQuery("dig @$server NS $domain +noall +answer");
    }
}

// Run test
$tester = new ShellDnsTester();
header('Content-Type: application/json');
echo json_encode([
    'a'  => $tester->aQuery('google.com', '8.8.8.8'),
    'ns' => $tester->nsQuery('google.com', '8.8.8.8')
], JSON_PRETTY_PRINT);
