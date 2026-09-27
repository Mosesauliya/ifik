<?php
$mysqli = new mysqli('100.83.19.18', 'ci3_user', 'ci3_password', 'db_ifik_baru');
$res = $mysqli->query("SELECT * FROM booking");
$rows = [];
while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
}
echo json_encode($rows, JSON_PRETTY_PRINT);
