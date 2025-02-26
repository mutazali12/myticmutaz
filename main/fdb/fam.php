<?php
session_start();
$host = 'localhost';
$dbname = 'mutazz';
$username = 'root';
$password = '';

// معالجة عمليات الحذف
if (isset($_GET['delete'])) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($_GET['type'] === 'trip') {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("DELETE FROM passenger_info WHERE trip_id = ?");
            $stmt->execute([$_GET['id']]);
            
            $stmt = $pdo->prepare("DELETE FROM trip_info WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $pdo->commit();
            
            $_SESSION['message'] = "تم حذف الأسرة وجميع أفرادها بنجاح";
        } else {
            $stmt = $pdo->prepare("DELETE FROM passenger_info WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $_SESSION['message'] = "تم حذف الفرد بنجاح";
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "خطأ في الحذف: " . $e->getMessage();
    }
}

// جلب البيانات
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT t.*, p.* 
            FROM trip_info t
            LEFT JOIN passenger_info p ON t.id = p.trip_id
            ORDER BY t.travel_date DESC, p.passenger_name ASC";

    $stmt = $pdo->query($sql);
    $families = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $familyId = $row['id'];
        if (!isset($families[$familyId])) {
            $families[$familyId] = [
                'trip' => [
                    'id' => $familyId,
                    'departure' => $row['departure_airport'],
                    'arrival' => $row['arrival_airport'],
                    'date' => $row['travel_date'],
                    'class' => $row['class'],
                    'phone' => $row['phone']
                ],
                'members' => []
            ];
        }
        if ($row['passenger_name']) {
            $families[$familyId]['members'][] = [
                'id' => $row['id'],
                'name' => $row['passenger_name'],
                'passport' => $row['passport_number'],
                'birth' => $row['birth_date'],
                'issue' => $row['passport_issue'],
                'expiry' => $row['passport_expiry']
            ];
        }
    }
} catch (PDOException $e) {
    die("فشل الاتصال: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة الأسر المسافرة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
        }

        .family-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            margin-bottom: 20px;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .family-header {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 15px;
            border-radius: 10px 10px 0 0;
        }

        .family-details {
            padding: 15px;
        }

        .member-table {
            width: 100%;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .member-table th {
            background: var(--primary-color);
            color: white;
        }

        .passport-status {
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }

        .valid { background: #d4edda; color: #155724; }
        .expired { background: #f8d7da; color: #721c24; }

        @media (max-width: 768px) {
            .responsive-table {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <?php if (isset($_SESSION['message'])) : ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message']); endif; ?>

        <h2 class="mb-4 text-center"><i class="fas fa-users"></i>الأسر و المجموعات</h2>

        <?php foreach ($families as $familyId => $family) : ?>
            <div class="family-card">
                <div class="family-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>
                            <i class="fas fa-plane-departure"></i> 
                            <?= htmlspecialchars($family['trip']['departure']) ?> 
                            ➔ 
                            <?= htmlspecialchars($family['trip']['arrival']) ?>
                        </h5>
                        <button class="btn btn-danger btn-sm" 
                                onclick="confirmDelete('trip', <?= $familyId ?>)">
                            <i class="fas fa-trash"></i> حذف الأسرة
                        </button>
                    </div>
                </div>

                <div class="family-details">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>تاريخ السفر:</strong> 
                            <?= date('Y-m-d', strtotime($family['trip']['date'])) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>فئة السفر:</strong> 
                            <?= htmlspecialchars($family['trip']['class']) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>هاتف الاتصال:</strong> 
                            <?= htmlspecialchars($family['trip']['phone']) ?>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3"><i class="fas fa-users"></i> أفراد الأسرة</h5>
                    
                    <div class="table-responsive">
                        <table class="table member-table">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>رقم الجواز</th>
                                    <th>تاريخ الميلاد</th>
                                    <th>إصدار الجواز</th>
                                    <th>انتهاء الجواز</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($family['members'] as $member) : 
                                    $passportStatus = (strtotime($member['expiry']) > time()) ? 'valid' : 'expired';
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($member['name']) ?></td>
                                        <td><?= htmlspecialchars($member['passport']) ?></td>
                                        <td><?= date('Y-m-d', strtotime($member['birth'])) ?></td>
                                        <td><?= date('Y-m-d', strtotime($member['issue'])) ?></td>
                                        <td><?= date('Y-m-d', strtotime($member['expiry'])) ?></td>
                                        <td>
                                            <span class="passport-status <?= $passportStatus ?>">
                                                <?= $passportStatus === 'valid' ? 'ساري' : 'منتهي' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-sm" 
                                                    onclick="confirmDelete('passenger', <?= $member['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal تأكيد الحذف -->
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-danger"></i> تأكيد الحذف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <a id="confirmDeleteBtn" href="#" class="btn btn-danger">حذف</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(type, id) {
            const message = type === 'trip' 
                ? 'هل تريد حذف هذه الأسرة وجميع أفرادها؟' 
                : 'هل تريد حذف هذا الفرد؟';
            
            document.getElementById('deleteMessage').textContent = message;
            document.getElementById('confirmDeleteBtn').href = `?delete=1&type=${type}&id=${id}`;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
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