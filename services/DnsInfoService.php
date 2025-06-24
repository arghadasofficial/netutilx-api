<?php

namespace Services;

use Core\DnsExecutor;

class DnsInfoService
{
    private DnsServerService $dnsServerService;
    private DnsTypeService $dnsTypeService;
    private DnsExecutor $dnsExecutor;

    public function __construct(DnsServerService $dnsServerService, DnsTypeService $dnsTypeService, DnsExecutor $dnsExecutor)
    {
        $this->dnsServerService = $dnsServerService;
        $this->dnsTypeService = $dnsTypeService;
        $this->dnsExecutor = $dnsExecutor;
    }

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
            case 'PTR':
                return ['success' => false, 'output' => "Error: The query type PTR is not supported for direct queries. Please use an IP address."];
            default:
                return ['success' => false, 'output' => "Error: The query type '{$type_name}' is not supported."];
        }
    }

    public function isIpAddress(string $query): bool
    {
        return filter_var($query, FILTER_VALIDATE_IP) !== false;
    }
}
