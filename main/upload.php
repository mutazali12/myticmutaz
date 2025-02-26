<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mutaz";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

$passport_number = $_POST['passport_number'];
$name = $_POST['name'];
$image = $_FILES['image']['name'];
$target = "uploads/" . basename($image);

if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
    $sql = "INSERT INTO images (passport_number, name, image) VALUES ('$passport_number', '$name', '$image')";
    if ($conn->query($sql) === TRUE) {
        echo "تم رفع الصورة بنجاح";
    } else {
        echo "خطأ: " . $conn->error;
    }
} else {
    echo "فشل رفع الصورة";
}

$conn->close();
?>