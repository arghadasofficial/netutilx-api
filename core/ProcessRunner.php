<?php

namespace Core;

use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class ProcessRunner
{
    /**
     * Executes any given command-line process and returns a standardized result.
     *
     * @param array $command The command and its arguments to run.
     * @param int $timeout The timeout in seconds.
     * @return array A standardized result array.
     */
    public function execute(array $command, int $timeout = 2): array
    {
        try {
            $process = new Process($command);
            $process->setTimeout($timeout);
            $process->run();

            if (!$process->isSuccessful()) {
                return [
                    'success' => false,
                    'query'   => implode(' ', $command),
                    'output'  => $process->getErrorOutput() ?: 'Command failed with no error output.'
                ];
            }

            $output = trim($process->getOutput());
            $isSuccess = !empty($output);

            return [
                'success' => $isSuccess,
                'query'   => implode(' ', $command),
                'output'  => $isSuccess ? $output : ($output ?: 'No response received.')
            ];
        } catch (ProcessTimedOutException $e) {
            return [
                'success' => false,
                'query'   => implode(' ', $command),
                'output'  => "Error: The command timed out after {$timeout} seconds."
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'query'   => implode(' ', $command),
                'output'  => 'Exception: ' . $e->getMessage()
            ];
        }
    }
}
