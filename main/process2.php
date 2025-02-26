<?php
// ملف insert.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mutazz";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// إدخال بيانات الرحلة
$departure_airport = $conn->real_escape_string($_POST['departure_airport']);
$arrival_airport = $conn->real_escape_string($_POST['arrival_airport']);
$travel_date = $conn->real_escape_string($_POST['travel_date']);
$class = $conn->real_escape_string($_POST['class']);
$phone = $conn->real_escape_string($_POST['phone']);

$sql_trip = "INSERT INTO trip_info (departure_airport, arrival_airport, travel_date, class, phone)
VALUES ('$departure_airport', '$arrival_airport', '$travel_date', '$class', '$phone')";

if ($conn->query($sql_trip)) {
    $trip_id = $conn->insert_id;
    
    // إدخال بيانات المسافرين
    $total_passengers = $_POST['num_travelers'] + $_POST['num_children'];
    
    for ($i = 1; $i <= $total_passengers; $i++) {
        $passenger_name = $conn->real_escape_string($_POST['name'.$i]);
        $passport_number = $conn->real_escape_string($_POST['passport_number'.$i]);
        $birth_date = $conn->real_escape_string($_POST['birth_date'.$i]);
        $passport_issue = $conn->real_escape_string($_POST['passport_issue'.$i]);
        $passport_expiry = $conn->real_escape_string($_POST['passport_expiry'.$i]);
        
        $sql_passenger = "INSERT INTO passenger_info (trip_id, passenger_name, passport_number, birth_date, passport_issue, passport_expiry)
        VALUES ('$trip_id', '$passenger_name', '$passport_number', '$birth_date', '$passport_issue', '$passport_expiry')";
        
        $conn->query($sql_passenger);
    }
    
    // الانتقال إلى صفحة النجاح
    header("Location: success.php");
    exit();
} else {
    // الانتقال إلى صفحة الخطأ
    header("Location: error.php");
    exit();
}

$conn->close();
?>
