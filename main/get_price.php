<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flights";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$departure = $_GET['departure'];
$arrival = $_GET['arrival'];

$sql = "SELECT price FROM prices WHERE departure_airport = '$departure' AND arrival_airport = '$arrival'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo $row['price'];
} else {
    echo "السعر غير متوفر";
}

$conn->close();
?>