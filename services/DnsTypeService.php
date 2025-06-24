<?php

namespace Services;

class DnsTypeService
{
    private $conn;
    private $table_name = 'dns_types';

    public function __construct($dbConnection)
    {
        $this->conn = $dbConnection;
    }

    public function getAllDnsTypes()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name ASC";

        $result = $this->conn->query($query);

        $typesArray = $result->fetch_all(MYSQLI_ASSOC);

        $result->free();

        return $typesArray;
    }

    public function getTypeById($typeId)
    {
        $query = "SELECT name FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $typeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $record = $result->fetch_assoc();
        $stmt->close();
        return $record;
    }
}
