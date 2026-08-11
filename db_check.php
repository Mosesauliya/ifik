<?php
$mysqli = new mysqli("db", "root", "rootpassword", "db_ifik");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "TABLES:\n";
$result = $mysqli->query("SHOW TABLES");
while($row = $result->fetch_array()) {
    echo $row[0] . "\n";
}

echo "\nUSERS:\n";
$result = $mysqli->query("DESCRIBE users");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "No users table\n";
}

echo "\nMAHASISWA:\n";
$result = $mysqli->query("DESCRIBE mahasiswa");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "No mahasiswa table\n";
}

echo "\nDOSEN:\n";
$result = $mysqli->query("DESCRIBE dosen");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "No dosen table\n";
}

echo "\nPEMINJAMAN:\n";
$result = $mysqli->query("DESCRIBE peminjaman");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "No peminjaman table\n";
}
?>
