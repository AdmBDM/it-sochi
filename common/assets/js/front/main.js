// Скрипты фронтенда
document.addEventListener('DOMContentLoaded', function() {
    // Мобильное меню
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
    }
});

function toggleMobileMenu() {
    document.querySelector('.main-nav').classList.toggle('active');
}
