document.addEventListener("DOMContentLoaded", function() {
    fetch('fetch_data.php')
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('dataContainer');
        if (data.length > 0 && !data[0].message) {
            let table = '<table><tr><th>اختيار</th><th>ID</th><th>اسم المسافر</th><th>رقم الجواز</th><th>تاريخ إصدار الجواز</th><th>تاريخ انتهاء الجواز</th><th>مطار المغادرة</th><th>مطار الوصول</th><th>تاريخ الرحلة</th><th>الدرجة</th><th>رقم الهاتف</th><th>عدد المسافرين</th><th>عدد الأطفال</th></tr>';
            data.forEach(row => {
                table += `<tr>
                            <td><input type='checkbox' name='ids[]' value='${row.id}'></td>
                            <td>${row.id}</td>
                            <td>${row.name}</td>
                            <td>${row.passport_number}</td>
                            <td>${row.passport_issue}</td>
                            <td>${row.passport_expiry}</td>
                            <td>${row.departure_airport}</td>
                            <td>${row.arrival_airport}</td>
                            <td>${row.travel_date}</td>
                            <td>${row.class}</td>
                            <td>${row.phone}</td>
                            <td>${row.num_travelers}</td>
                            <td>${row.num_children}</td>
                          </tr>`;
            });
            table += '</table>';
            container.innerHTML = table;
        } else {
            container.innerHTML = "<p>لا توجد بيانات</p>";
        }
    })
    .catch(error => console.error('Error:', error));
});
