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
        // Execute the query
        $result = $this->conn->query($query);

        // Fetch all records into an array
        $typesArray = $result->fetch_all(MYSQLI_ASSOC);

        // Free the result set
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
