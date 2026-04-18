// Скрипты бэкенда
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin panel initialized');

    // Инициализация сайдбара
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
        });
    }
});
