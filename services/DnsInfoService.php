<?php

namespace Services;

use Core\DnsExecutor;

class DnsInfoService
{
    private DnsServerService $dnsServerService;
    private DnsTypeService $dnsTypeService;
    private DnsExecutor $dnsExecutor;

    /**
     * The constructor is now simpler. It no longer needs the database connection
     * as this class is not directly interacting with the database anymore.
     */
    public function __construct(DnsServerService $dnsServerService, DnsTypeService $dnsTypeService, DnsExecutor $dnsExecutor)
    {
        $this->dnsServerService = $dnsServerService;
        $this->dnsTypeService = $dnsTypeService;
        $this->dnsExecutor = $dnsExecutor;
    }

    /**
     * Performs a live DNS query without saving the result.
     */
    public function queryDnsInfo(string $query, int $serverId, int $typeId): array
    {
        $server = $this->dnsServerService->getServerById($serverId);
        if (!$server) {
            return ['success' => false, 'output' => 'Error: The specified Server ID is invalid.'];
        }

        if ($this->isIpAddress($query)) {
            return $this->dnsExecutor->ptrQuery($query);
        }

        $type = $this->dnsTypeService->getTypeById($typeId);
        if (!$type) {
            return ['success' => false, 'output' => 'Error: The specified Type ID is invalid.'];
        }

        $type_name = $type['name'];

        // 4. Use the type name to call the correct executor method
        switch ($type_name) {
            case 'A':
                return $this->dnsExecutor->aQuery($query, $server['ip_address']);
            case 'NS':
                return $this->dnsExecutor->nsQuery($query, $server['ip_address']);
            case 'MX':
                return $this->dnsExecutor->mxQuery($query, $server['ip_address']);
            case 'SOA':
                return $this->dnsExecutor->soaQuery($query, $server['ip_address']);
            case 'TXT':
                return $this->dnsExecutor->txtQuery($query, $server['ip_address']);
            default:
                return ['success' => false, 'output' => "Error: The query type '{$type_name}' is not supported."];
        }
    }

    /**
     * Checks if a string is a valid IP address.
     */
    public function isIpAddress(string $query): bool
    {
        return filter_var($query, FILTER_VALIDATE_IP) !== false;
    }
}
