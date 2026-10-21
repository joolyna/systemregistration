
<?php

require_once 'config/connection.php';

$database = new Database();
$db = $database->connect();

if ($db) {
    echo "Database connected successfully!";
}

?>
