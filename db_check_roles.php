<?php
$mysqli = new mysqli("db", "root", "rootpassword", "db_ifik");

echo "ROLES:\n";
$result = $mysqli->query("SELECT * FROM roles");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['id'] . " - " . $row['name'] . "\n";
    }
}
?>
