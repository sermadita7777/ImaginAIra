// js/profile.js
document.addEventListener('DOMContentLoaded', () => {
  // 1) Llamamos a get_user_likes.php para obtener el array de IDs de contenidos que el usuario ya ha "likeado"
  fetch('get_user_likes.php')
    .then(res => res.json())
    .then(userLikesArray => {
      // userLikesArray debería ser algo como [3, 17, 25, ...]
      const favCountEl = document.getElementById('favCount');
      if (favCountEl) {
        // Actualizamos el contador con la longitud del array
        favCountEl.textContent = Array.isArray(userLikesArray)
          ? userLikesArray.length
          : 0;
      }
    })
    .catch(err => {
      console.error('Error al obtener favoritos:', err);
      // Dejamos el contador a 0 si algo falla
    });
});
