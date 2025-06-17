<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

try {
    // Test dig command on google.com for A record
    $process = new Process(['dig', 'google.com', 'A', '+noall', '+answer']);
    $process->run();

    // Check if dig was successful
    if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }

    echo "✅ 'dig' executed successfully.\n";
    echo "Output:\n" . $process->getOutput();
} catch (\Throwable $e) {
    echo "❌ Process or 'dig' failed.\n";
    echo "Error: " . $e->getMessage();
}
