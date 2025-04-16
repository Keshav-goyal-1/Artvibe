<?php
$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM artworks";
$result = $conn->query($sql);
$artworks = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $artworks[] = $row;
    }
}

header("Content-Type: application/json");
echo json_encode($artworks);

$conn->close();
?>