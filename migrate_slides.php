<?php
$conn = new mysqli('100.83.19.18', 'ci3_user', 'ci3_password', 'db_ifik');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure header_settings exists (created in previous step, but let's be safe)
$sql = "CREATE TABLE IF NOT EXISTS `header_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `dekanat_image` varchar(255) DEFAULT 'dekanat2.png',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($sql);

$sql_slides = "CREATE TABLE IF NOT EXISTS `header_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(50) DEFAULT NULL,
  `media_type` varchar(20) DEFAULT 'image',
  `media_path` varchar(255) DEFAULT NULL,
  `order_num` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($sql_slides);

// Insert default slides if empty
$res = $conn->query("SELECT * FROM header_slides");
if ($res->num_rows == 0) {
    $conn->query("INSERT INTO header_slides (label, media_type, media_path, order_num, created_at) VALUES ('Overview', 'image', 'Fakultas.jpg', 1, NOW())");
    $conn->query("INSERT INTO header_slides (label, media_type, media_path, order_num, created_at) VALUES ('Fasilitas', 'video', 'vidtelkom.mp4', 2, NOW())");
    $conn->query("INSERT INTO header_slides (label, media_type, media_path, order_num, created_at) VALUES ('Prestasi', 'image', 'background.png', 3, NOW())");
}

echo "Database updated successfully.\n";
$conn->close();
?>
