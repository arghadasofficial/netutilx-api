<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'partials/head.inc.php'; 
?>

<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;

$database = new Database();
$db = $database->connect();

$query = "SELECT * FROM " . 'dns_servers' . " ORDER BY name ASC";

$result = $db->query($query);

$typesArray = $result->fetch_all(MYSQLI_ASSOC);

$result->free();

print_r($typesArray);

?>

<?php require 'partials/foot.inc.php'; ?>