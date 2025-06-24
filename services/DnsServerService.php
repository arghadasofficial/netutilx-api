<?php

namespace Services;

class DnsServerService
{
    private $conn;
    private $table_name = 'dns_servers';

    public function __construct($dbConnection)
    {
        $this->conn = $dbConnection;
    }

    public function getAllDnsServers()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name ASC";

        $result = $this->conn->query($query);

        $serversArray = $result->fetch_all(MYSQLI_ASSOC);

        $result->free();

        return $serversArray;
    }

    public function getServerById($serverId){
        $query = "SELECT ip_address FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $serverId);
        $stmt->execute();
        $result = $stmt->get_result();
        $record = $result->fetch_assoc();
        $stmt->close();
        return $record;
    }
}
