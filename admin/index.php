<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;

$database = new Database();
$db = $database->connect();

$query = "SELECT * FROM " . $this->table_name . " ORDER BY name ASC";

$result = $db->query($query);

$typesArray = $result->fetch_all(MYSQLI_ASSOC);

$result->free();

print_r($typesArray);

?>

<?php require 'partials/head.inc.php'; ?>

<?php require 'partials/foot.inc.php'; ?>