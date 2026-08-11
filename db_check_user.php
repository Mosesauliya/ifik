<?php
$mysqli = new mysqli("db", "root", "rootpassword", "db_ifik");

$result = $mysqli->query("SELECT nidn_nim, name FROM users LIMIT 1");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "Valid user: " . $row['nidn_nim'] . " or " . $row['name'] . "\n";
    }
}
?>
