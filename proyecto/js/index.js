
// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar la funcionalidad del menú hamburguesa para dispositivos móviles
    initMobileMenu();
});

/**
 * Inicializa el menú hamburguesa para dispositivos móviles
 */
function initMobileMenu() {
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    const navLinksItems = document.querySelectorAll('.nav-links a');

    if (hamburger) {
        // Toggle menú al hacer clic en hamburguesa
        hamburger.addEventListener('click', function () {
            navLinks.classList.toggle('active');
            hamburger.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });

        // Cerrar menú al hacer clic en un enlace
        navLinksItems.forEach(item => {
            item.addEventListener('click', function () {
                if (window.innerWidth <= 768) {
                    navLinks.classList.remove('active');
                    hamburger.classList.remove('active');
                    document.body.classList.remove('menu-open');
                }
            });
        });

        // Cerrar menú al hacer clic fuera del menú
        document.addEventListener('click', function (event) {
            const isClickInsideMenu = navLinks.contains(event.target);
            const isClickOnHamburger = hamburger.contains(event.target);

            if (!isClickInsideMenu && !isClickOnHamburger && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                hamburger.classList.remove('active');
                document.body.classList.remove('menu-open');
            }
        });

        // Ajustar menú en cambio de tamaño de ventana
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768 && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                hamburger.classList.remove('active');
                document.body.classList.remove('menu-open');
            }
        });
    }
}


