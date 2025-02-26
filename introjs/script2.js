window.addEventListener('load', () => {
  const watermark = document.querySelector('.watermark');
  watermark.style.opacity = 1;

  setTimeout(() => {
    window.location.href = 'main/main.html'; // استبدل your-link-here.html بعنوان الصفحة التي تريد الانتقال إليها
  }, 5000); // 5000 ميلي ثانية = 5 ثواني (يمكنك تغيير هذه المدة)
});