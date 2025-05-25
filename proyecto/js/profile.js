/**
 * IA-Lovers - Profile Scripts
 */

// Variable global para el usuario actual
let user = localStorage.getItem('currentUser') || 'Usuario';

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar la funcionalidad del menú hamburguesa para dispositivos móviles
    initMobileMenu();
    
    // Comprobar si hay un usuario logueado
    checkUserLogin();
    
    // Inicializar funcionalidades específicas del perfil
    initProfileFunctionality();
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
        hamburger.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            hamburger.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });
        
        // Cerrar menú al hacer clic en un enlace
        navLinksItems.forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    navLinks.classList.remove('active');
                    hamburger.classList.remove('active');
                    document.body.classList.remove('menu-open');
                }
            });
        });
        
        // Cerrar menú al hacer clic fuera del menú
        document.addEventListener('click', function(event) {
            const isClickInsideMenu = navLinks.contains(event.target);
            const isClickOnHamburger = hamburger.contains(event.target);
            
            if (!isClickInsideMenu && !isClickOnHamburger && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                hamburger.classList.remove('active');
                document.body.classList.remove('menu-open');
            }
        });
        
        // Ajustar menú en cambio de tamaño de ventana
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                hamburger.classList.remove('active');
                document.body.classList.remove('menu-open');
            }
        });
    }
}

/**
 * Comprueba si hay un usuario logueado y actualiza la interfaz
 */
function checkUserLogin() {
    const currentUser = localStorage.getItem('currentUser');
    const authNav = document.getElementById('authNav');
    const userNav = document.getElementById('userNav');
    
    if (authNav && userNav) {
        authNav.classList.toggle('hidden', !!currentUser);
        userNav.classList.toggle('hidden', !currentUser);
    }
    
    // Actualizar la variable global del usuario
    if (currentUser) {
        user = currentUser;
    }
}

/**
 * Inicializa la funcionalidad específica del perfil
 */
function initProfileFunctionality() {
    // Mostrar el nombre de usuario
    const profileUserElement = document.getElementById('profileUser');
    if (profileUserElement) {
        profileUserElement.textContent = user;
    }
    
    // Mostrar fecha de registro (simulada)
    const memberSinceElement = document.getElementById('memberSince');
    if (memberSinceElement) {
        const date = new Date();
        memberSinceElement.textContent = date.toLocaleDateString();
    }
    
    // Contar elementos favoritos
    updateFavoritesCount();
    
    // Contar elementos en el carrito
    updateCartCount();
    
    // Evento para cerrar sesión
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                logoutUser();
            }
        });
    }
}

/**
 * Actualiza el contador de elementos favoritos
 */
function updateFavoritesCount() {
    const favCountElement = document.getElementById('favCount');
    if (favCountElement) {
        const likedItems = JSON.parse(localStorage.getItem('likedItems')) || [];
        favCountElement.textContent = likedItems.length;
    }
}

/**
 * Actualiza el contador de elementos en el carrito
 */
function updateCartCount() {
    const cartItemCountElement = document.getElementById('cartItemCount');
    if (cartItemCountElement) {
        const cartItems = JSON.parse(localStorage.getItem('iaCart')) || [];
        cartItemCountElement.textContent = cartItems.length;
    }
}


/**
 * Función para cerrar sesión
 */
window.logoutUser = function() {
    localStorage.removeItem('currentUser');
    window.location.href = 'login.html';
};

/**
 * Función para actualizar los contadores del perfil
 * Útil para llamar desde otras páginas cuando se modifiquen favoritos o carrito
 */
window.updateProfileCounters = function() {
    updateFavoritesCount();
    updateCartCount();
};