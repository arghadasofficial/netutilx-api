<?php

namespace Argha\NetutilxApi\Helpers;

class PingHelper
{

    function parsePingResponse($output)
    {
        // Split output by lines, filter empty ones, and re-index the array
        $lines = array_values(array_filter(explode("\n", trim($output))));
        $result = [];

        // Extract the header line
        $header = array_shift($lines);
        // Example header: "PING crudoimage.com (89.116.20.177) 56(84) bytes of data."
        if (preg_match('/^PING\s+(\S+)\s+\((\S+)\)\s+(\d+)\((\d+)\)\s+bytes\s+of\s+data\./', $header, $headerMatches)) {
            $result['destination'] = [
                'hostname'    => $headerMatches[1],
                'ip'          => $headerMatches[2],
                'bytes'       => (int)$headerMatches[3],
                'total_bytes' => (int)$headerMatches[4]
            ];
        }

        $result['replies'] = [];
        // Process reply lines until we reach the separator line
        while (!empty($lines)) {
            $line = array_shift($lines);
            // If we hit the separator line, break out of the loop
            if (strpos($line, '---') === 0) {
                break;
            }
            // Parse each reply line
            // Example: "64 bytes from server.grow10x.business (89.116.20.177): icmp_seq=1 ttl=64 time=0.043 ms"
            if (preg_match('/^(\d+)\s+bytes\s+from\s+(\S+)\s+\((\S+)\):\s+icmp_seq=(\d+)\s+ttl=(\d+)\s+time=([\d\.]+)\s+ms/', $line, $replyMatches)) {
                $result['replies'][] = [
                    'bytes'    => (int)$replyMatches[1],
                    'source'   => $replyMatches[2],
                    'ip'       => $replyMatches[3],
                    'icmp_seq' => (int)$replyMatches[4],
                    'ttl'      => (int)$replyMatches[5],
                    'time_ms'  => (float)$replyMatches[6],
                ];
            }
        }

        // Next, the summary line should be the next line
        $summaryLine = array_shift($lines);
        if (preg_match('/^(\d+)\s+packets\s+transmitted,\s+(\d+)\s+received,\s+([\d\.]+)%\s+packet\s+loss,\s+time\s+(\d+)ms/', $summaryLine, $summaryMatches)) {
            $result['summary'] = [
                'packets_transmitted' => (int)$summaryMatches[1],
                'packets_received'    => (int)$summaryMatches[2],
                'packet_loss_percent' => (float)$summaryMatches[3],
                'time_ms'             => (int)$summaryMatches[4],
            ];
        }

        // Finally, the RTT line
        $rttLine = array_shift($lines);
        if (preg_match('/^rtt\s+min\/avg\/max\/mdev\s+=\s+([\d\.]+)\/([\d\.]+)\/([\d\.]+)\/([\d\.]+)\s+ms/', $rttLine, $rttMatches)) {
            $result['rtt'] = [
                'min'  => (float)$rttMatches[1],
                'avg'  => (float)$rttMatches[2],
                'max'  => (float)$rttMatches[3],
                'mdev' => (float)$rttMatches[4],
            ];
        }

        return $result;
    }
}
