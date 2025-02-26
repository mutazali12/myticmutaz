<?php
include('config1.php');
// معالجة عمليات الحذف
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM flights WHERE id=$id");
}

// معالجة عمليات الإضافة والتعديل
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $departure = mysqli_real_escape_string($conn, $_POST['departure']);
    $arrival = mysqli_real_escape_string($conn, $_POST['arrival']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    
    if(isset($_POST['id'])){ // التعديل
        $id = $_POST['id'];
        mysqli_query($conn, "UPDATE flights SET departure_airport='$departure', arrival_airport='$arrival', price=$price WHERE id=$id");
    } else { // الإضافة
        mysqli_query($conn, "INSERT INTO flights (departure_airport, arrival_airport, price) VALUES ('$departure', '$arrival', $price)");
    }
}

$flights = mysqli_query($conn, "SELECT * FROM flights");
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الرحلات الجوية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .table-container { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .table thead th { background: #007bff; color: white; border: none; }
        .btn-custom { background: #007bff; color: white; transition: all 0.3s; }
        .btn-custom:hover { background: #0056b3; }
        .table-hover tbody tr:hover { background-color: rgba(0,123,255,0.05); }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4 text-center">إدارة الرحلات الجوية ✈️</h1>
        
        <div class="table-container p-4 mb-4">
            <button class="btn btn-custom mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
                إضافة رحلة جديدة ➕
            </button>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>مطار المغادرة</th>
                        <th>مطار الوصول</th>
                        <th>السعر (USD)</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($flights)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['departure_airport'] ?></td>
                        <td><?= $row['arrival_airport'] ?></td>
                        <td><?= number_format($row['price'], 2) ?></td>
                        <td>
                            <button class="btn btn-sm btn-custom edit-btn" 
                                    data-id="<?= $row['id'] ?>"
                                    data-departure="<?= $row['departure_airport'] ?>"
                                    data-arrival="<?= $row['arrival_airport'] ?>"
                                    data-price="<?= $row['price'] ?>">
                                تعديل ✏️
                            </button>
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                حذف 🗑️
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- نموذج الإضافة -->
    <div class="modal fade" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">إضافة رحلة جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>مطار المغادرة</label>
                            <input type="text" name="departure" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>مطار الوصول</label>
                            <input type="text" name="arrival" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>السعر</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-custom">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- نموذج التعديل -->
    <div class="modal fade" id="editModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="id" id="editId">
                    <div class="modal-header">
                        <h5 class="modal-title">تعديل الرحلة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>مطار المغادرة</label>
                            <input type="text" name="departure" id="editDeparture" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>مطار الوصول</label>
                            <input type="text" name="arrival" id="editArrival" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>السعر</label>
                            <input type="number" step="0.01" name="price" id="editPrice" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-custom">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // معالجة حدث التعديل
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('editId').value = btn.dataset.id;
                document.getElementById('editDeparture').value = btn.dataset.departure;
                document.getElementById('editArrival').value = btn.dataset.arrival;
                document.getElementById('editPrice').value = btn.dataset.price;
                new bootstrap.Modal(document.getElementById('editModal')).show();
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