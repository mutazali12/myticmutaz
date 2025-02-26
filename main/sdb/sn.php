<?php
session_start();
// تكوين الاتصال بقاعدة البيانات
$host = 'localhost';
$dbname = 'mutaz';
$username = 'root'; // غيّر لاسم مستخدم قاعدة البيانات
$password = '';     // غيّر لكلمة مرور قاعدة البيانات

// معالجة طلب الحذف
if (isset($_GET['delete'])) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("DELETE FROM travelers WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        
        $_SESSION['message'] = "تم حذف السجل بنجاح";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch(PDOException $e) {
        $_SESSION['error'] = "خطأ في الحذف: " . $e->getMessage();
    }
}

// جلب بيانات المسافرين
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM travelers");
    $travelers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "فشل الاتصال: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة المسافرين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --main-color: #2c3e50;
            --secondary-color: #3498db;
            --hover-color: #2980b9;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Tajawal', sans-serif;
        }
        
        .container {
            animation: fadeIn 0.5s ease-in;
        }
        
        .table-custom {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        
        .table-custom thead {
            background: linear-gradient(45deg, var(--main-color), var(--secondary-color));
            color: white;
        }
        
        .table-custom th {
            border: none;
            font-weight: 700;
            text-align: center;
            padding: 15px;
        }
        
        .table-custom td {
            vertical-align: middle;
            text-align: center;
            transition: all 0.3s ease;
            padding: 12px;
        }
        
        .table-custom tbody tr {
            background: white;
            transition: transform 0.3s ease;
        }
        
        .table-custom tbody tr:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .btn-custom {
            background: var(--secondary-color);
            color: white;
            border-radius: 25px;
            transition: all 0.3s ease;
            padding: 8px 20px;
        }
        
        .btn-custom:hover {
            background: var(--hover-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 15px;
            margin: 20px 0;
        }

        .dark-mode {
            background-color: #1a1a1a;
            color: #ffffff;
        }
        
        .dark-mode .table-custom thead {
            background: linear-gradient(45deg, #34495e, #2c3e50);
        }
        
        .dark-mode .table-custom tbody tr {
            background-color: #2c3e50;
            color: white;
        }
        
        .dark-mode .table-custom tbody tr:hover td {
            background-color: #34495e;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary">
                <i class="fas fa-users me-2"></i>الافراد 
            </h1>
            <button class="btn btn-custom" onclick="toggleDarkMode()">
                <i class="fas fa-moon"></i> وضع الليل
            </button>
        </div>
        
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-success d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <?= $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-custom table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم الكامل</th>
                        <th>رقم الجواز</th>
                        <th>تاريخ الإصدار</th>
                        <th>انتهاء الصلاحية</th>
                        <th>مطار المغادرة</th>
                        <th>مطار الوصول</th>
                        <th>تاريخ السفر</th>
                        <th>الفئة</th>
                        <th>الهاتف</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($travelers as $traveler): ?>
                    <tr>
                        <td><?= htmlspecialchars($traveler['id']) ?></td>
                        <td><?= htmlspecialchars($traveler['name']) ?></td>
                        <td><?= htmlspecialchars($traveler['passport_number']) ?></td>
                        <td><?= date('Y-m-d', strtotime($traveler['passport_issue'])) ?></td>
                        <td><?= date('Y-m-d', strtotime($traveler['passport_expiry'])) ?></td>
                        <td><?= htmlspecialchars($traveler['departure_airport']) ?></td>
                        <td><?= htmlspecialchars($traveler['arrival_airport']) ?></td>
                        <td><?= date('Y-m-d', strtotime($traveler['travel_date'])) ?></td>
                        <td>
                            <span class="badge bg-primary">
                                <?= htmlspecialchars($traveler['class']) ?>
                            </span>
                        </td>
                        <td dir="ltr"><?= htmlspecialchars($traveler['phone']) ?></td>
                        <td>
                            <a href="?delete=<?= $traveler['id'] ?>" 
                               class="btn btn-custom btn-sm"
                               onclick="confirmDelete(event)">
                               <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- نافذة تأكيد الحذف -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        تأكيد الحذف
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    هل أنت متأكد من رغبتك في حذف هذا السجل؟
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <a id="deleteLink" href="#" class="btn btn-danger">حذف</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // تأكيد الحذف باستخدام Modal
        function confirmDelete(event) {
            event.preventDefault();
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            document.getElementById('deleteLink').href = event.target.closest('a').href;
            deleteModal.show();
        }

        // تبديل وضع الليل
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const isDarkMode = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDarkMode);
            
            // تغيير أيقونة الزر
            const icon = document.querySelector('#darkModeButton i');
            if (isDarkMode) {
                icon.classList.replace('fa-moon', 'fa-sun');
            } else {
                icon.classList.replace('fa-sun', 'fa-moon');
            }
        }

        // تطبيق وضع الليل عند التحميل
        window.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('darkMode') === 'true') {
                document.body.classList.add('dark-mode');
                document.querySelector('#darkModeButton i').classList.replace('fa-moon', 'fa-sun');
            }
        });

        // إضافة تأثيرات لصفوف الجدول
        document.querySelectorAll('.table-custom tbody tr').forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.style.transform = 'scale(1.02)';
                row.style.zIndex = '10';
            });
            
            row.addEventListener('mouseleave', () => {
                row.style.transform = 'scale(1)';
                row.style.zIndex = '0';
            });
        });
    </script>


<style>
        /* تنسيقات البطاقة */
        .management-card {
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin: 10px;
        }

        a.management-card {
            display: block;
            text-decoration: none;
            color: inherit;
        }

        .management-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        /* تنسيقات الفوتر */
        .custom-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }

        .footer-link {
            text-decoration: none;
            color: #6c757d;
            font-family: Arial, sans-serif;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .footer-link:hover {
            color: #007bff;
            transform: scale(1.05);
        }

        .footer-link:active {
            transform: scale(0.95);
        }

        body {
            padding-bottom: 70px; /* لمنع تداخل المحتوى مع الفوتر */
        }
    </style>
    <footer class="custom-footer">
        <a href="مسار_الصفحة_الهدف.html" class="footer-link">
            bymutazali
        </a>
    </footer>


</body>
</html>