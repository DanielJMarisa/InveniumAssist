document.addEventListener('DOMContentLoaded', function () {

    const toggle = document.getElementById('mobileMenuToggle');
    const sidebar = document.querySelector('.app-sidebar');

    if (!toggle || !sidebar) {
        return;
    }

    toggle.addEventListener('click', function () {
        sidebar.classList.toggle('is-open');
    });

});