<?php
// بيانات الاتصال بقاعدة بيانات المستخدمين (travelers)
$servername_travelers = "localhost";
$username_travelers = "root";
$password_travelers = "";
$dbname_travelers = "travelers";

// بيانات الاتصال بقاعدة بيانات الأسعار
$servername_prices = "localhost";
$username_prices = "root";
$password_prices = "";
$dbname_prices = "prices"; // اسم قاعدة بيانات الأسعار

// إنشاء اتصال بقاعدة بيانات المستخدمين
$conn_travelers = new mysqli($servername_travelers, $username_travelers, $password_travelers, $dbname_travelers);

if ($conn_travelers->connect_error) {
    die("<p class='error'>فشل الاتصال بقاعدة بيانات المستخدمين: " . $conn_travelers->connect_error . "</p>");
}

// إنشاء اتصال بقاعدة بيانات الأسعار
$conn_prices = new mysqli($servername_prices, $username_prices, $password_prices, $dbname_prices);

if ($conn_prices->connect_error) {
    die("<p class='error'>فشل الاتصال بقاعدة بيانات الأسعار: " . $conn_prices->connect_error . "</p>");
}

// استلام البيانات من النموذج
$name = $_POST['name'];
$passport_number = $_POST['passport_number'];
$passport_issue = $_POST['passport_issue'];
$passport_expiry = $_POST['passport_expiry'];
$departure_airport = $_POST['departure_airport'];
$arrival_airport = $_POST['arrival_airport'];
$travel_date = $_POST['travel_date'];
$class = $_POST['class'];
$phone = $_POST['phone'];

// استعلام لجلب التكلفة من قاعدة بيانات الأسعار
$sql_price = "SELECT price FROM prices WHERE departure_airport = '$departure_airport' AND arrival_airport = '$arrival_airport'";
$result_price = $conn_prices->query($sql_price);

if ($result_price->num_rows > 0) {
    $row_price = $result_price->fetch_assoc();
    $cost = $row_price['price']; // جلب التكلفة من قاعدة بيانات الأسعار
} else {
    $cost = "غير متوفر"; // في حالة عدم وجود سعر
}

// إدراج البيانات في قاعدة بيانات المستخدمين
$sql_travelers = "INSERT INTO travelers (name, passport_number, passport_issue, passport_expiry, departure_airport, arrival_airport, travel_date, class, phone, cost)
                VALUES ('$name', '$passport_number', '$passport_issue', '$passport_expiry', '$departure_airport', '$arrival_airport', '$travel_date', '$class', '$phone', '$cost')";

if ($conn_travelers->query($sql_travelers) === TRUE) {
    // إنشاء رسالة النجاح وملخص الحجز
    $message = "<p class='success'>تم إرسال بيانات الحجز بنجاح</p>";
    $results = "<div class='section'>";
    $results .= "<h2>ملخص الحجز</h2>";
    $results .= "<p>الاسم: $name</p>";
    $results .= "<p>رقم جواز السفر: $passport_number</p>";
    $results .= "<p>تاريخ السفر: $travel_date</p>";
    $results .= "<p>مطار المغادرة: $departure_airport</p>";
    $results .= "<p>مطار الوصول: $arrival_airport</p>";
    $results .= "<p>الدرجة: $class</p>";
    $results .= "<p>رقم الهاتف واتساب: $phone</p>";
    $results .= "<p>التكلفة: $cost ريال</p>";
    $results .= "</div>";

    // إنشاء تفاصيل الحسابات البنكية
    $results .= "<div class='section'>";
    $results .= "<h2>تفاصيل الحسابات البنكية</h2>";
    $results .= "<p>البنك الأول: البنك الوطني - رقم الحساب: 12345678 - IBAN: SA123456789</p>";
    $results .= "<p>البنك الثاني: بنك الرياض - رقم الحساب: 87654321 - IBAN: SA987654321</p>";
    $results .= "</div>";

    // إنشاء نموذج رفع الصورة
    $results .= "<div class='section upload-form'>";
    $results .= "<h2>رفع صورة</h2>";
    $results .= "<form action='upload.php' method='post' enctype='multipart/form-data'>";
    $results .= "<input type='file' name='image' accept='image/*' required>";
    $results .= "<input type='hidden' name='passport_number' value='$passport_number'>";
    $results .= "<input type='hidden' name='name' value='$name'>";
    $results .= "<input type='submit' value='رفع الصورة'>";
    $results .= "</form>";
    $results .= "</div>";

    // إنشاء زر الإكمال
    $results .= "<button class='complete-btn'>إكمال</button>";

    // تخزين الرسالة والنتائج في الجلسة
    session_start();
    $_SESSION['message'] = $message;
    $_SESSION['results'] = $results;

    // إعادة التوجيه إلى results.php
    header("Location: results.php");
    exit;
} else {
    // إنشاء رسالة الخطأ
    session_start();
    $_SESSION['message'] = "<p class='error'>خطأ في إرسال البيانات: " . $conn_travelers->error . "</p>";

    // إعادة التوجيه إلى results.php
    header("Location: results.php");
    exit;
}

// إغلاق الاتصالات بقواعد البيانات
$conn_travelers->close();
$conn_prices->close();
?>