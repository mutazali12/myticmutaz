document.addEventListener('DOMContentLoaded', function() {
    const completeBtn = document.querySelector('.complete-btn');
    if (completeBtn) {
        completeBtn.addEventListener('click', function() {
            alert('تم إكمال الحجز بنجاح سيتم التواصل معك واتساب!');
        });
    }
});