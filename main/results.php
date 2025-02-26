<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتائج الحجز</title>
    <link rel="stylesheet" href="style5.css">
</head>
<body>
    <div class="container">
        <?php
        session_start();
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['results'])) {
            echo $_SESSION['results'];
            unset($_SESSION['results']);
        }
        ?>
    </div>
    <script src="script5.js"></script>
</body>
</html>