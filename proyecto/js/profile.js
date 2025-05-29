// js/profile.js
document.addEventListener('DOMContentLoaded', () => {
  // Favoritos: llamar al endpoint para obtener array de likes
  fetch(`/api/user/${encodeURIComponent(document.querySelector('.profile-info h3 span').textContent)}/likes`)
    .then(res => res.json())
    .then(data => {
      const favCountEl = document.getElementById('favCount');
      if (favCountEl) favCountEl.textContent = Array.isArray(data) ? data.length : 0;
    })
    .catch(() => {
      // Si no hay API, lo dejamos a 0 o podrías ocultar
    });

  // Carrito: leer de localStorage
  const cart = JSON.parse(localStorage.getItem('iaCart') || '[]');
  const cartCountEl = document.getElementById('cartItemCount');
  if (cartCountEl) cartCountEl.textContent = cart.length;
});
