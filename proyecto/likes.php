<?php
// likes.php
require 'header.php';
if (!$user) {
  echo "<section style='padding: 50px; text-align: center; font-family: Poppins, sans-serif;'>
          <h2 style='color: #c00;'>Acceso denegado</h2>
          <p>Debes iniciar sesión para acceder a tu perfil.</p>
          <a href='login.php' class='btn-primary' style='margin-top: 20px; display: inline-block;'>Iniciar sesión</a>
        </section>";
  require 'footer.php';
  exit;
}

// 1) Obtener id_usuario
$stmtU = $pdo->prepare("SELECT id_usuarios FROM usuarios WHERE nombre_usuario = ?");
$stmtU->execute([$user]);
$id_usuario = $stmtU->fetchColumn();

// 2) Si existe, obtener todos los contenidos que el usuario haya “likeado”
//    junto con el nombre del autor y si tiene miniatura
$likedItems = [];
if ($id_usuario) {
  $stmt = $pdo->prepare("
    SELECT 
      c.id_contenido, 
      c.titulo, 
      c.descripcion, 
      c.tipo_mime,
      c.miniatura_blob IS NOT NULL AS tiene_miniatura,
      u.nombre_usuario AS autor
    FROM contenido_likes cl
    JOIN contenidos c ON cl.contenido_id = c.id_contenido
    JOIN usuarios u ON c.usuario_id = u.id_usuarios
    WHERE cl.usuario_id = ?
    ORDER BY cl.created_at DESC
  ");
  $stmt->execute([$id_usuario]);
  $likedItems = $stmt->fetchAll();
}
?>
<link rel="stylesheet" href="css/likes.css" />
<link
  href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
  rel="stylesheet"
/>

<main class="section">
  <div class="container">
    <h2>Publicaciones que me gustaron</h2>
    <div id="likesGrid" class="grid">
      <?php if (empty($likedItems)): ?>
        <p class="no-items">Aún no has marcado ningún contenido como favorito.</p>
      <?php else: ?>
        <?php foreach ($likedItems as $post): ?>
          <div class="card" data-id="<?= $post['id_contenido'] ?>">
            <?php
              // Si es PDF:
              if ($post['tipo_mime'] === 'application/pdf'):
                // Si tiene miniatura, la mostramos
                if ($post['tiene_miniatura']): ?>
                  <img
                    src="thumbnail.php?id_contenido=<?= $post['id_contenido'] ?>&thumb=1"
                    alt="Miniatura de <?= htmlspecialchars($post['titulo']) ?>"
                  />
                <?php else: ?>
                  <i class="fas fa-file-pdf pdf-icon"></i>
                <?php endif;
              else:
                // Si NO es PDF, siempre mostramos su blob (imagen JPG/PNG)
                ?>
                <img
                  src="thumbnail.php?id_contenido=<?= $post['id_contenido'] ?>"
                  alt="<?= htmlspecialchars($post['titulo']) ?>"
                />
            <?php endif; ?>

            <div class="card-body">
              <!-- TÍTULO -->
              <h3><?= htmlspecialchars($post['titulo']) ?></h3>
              <!-- AUTOR -->
              <p class="card-author">
                Publicado por: <strong><?= htmlspecialchars($post['autor']) ?></strong>
              </p>
              <!-- DESCRIPCIÓN (truncada a 3 líneas) -->
              <p class="card-desc">
                <?= nl2br(htmlspecialchars($post['descripcion'])) ?>
              </p>
              <div class="card-actions">
                <!-- El botón “like” aparece ya marcado -->
                <button class="like-btn liked">
                  <i class="fas fa-heart"></i>
                  <span class="likes-count">0</span>
                </button>
                <button onclick="addToCart(<?= $post['id_contenido'] ?>)">
                  <i class="fas fa-cart-plus"></i>
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</main>

<script>
// ──────────────────────────────────────────────────────────────────────
// 1) CARRITO
// ──────────────────────────────────────────────────────────────────────
function addToCart(id) {
  fetch('add_to_cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id_contenido=' + encodeURIComponent(id)
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'ok') {
      alert('Añadido al carrito.');
      // Actualiza el contador de la cabecera
      fetch('cart_count.php')
        .then(res => res.json())
        .then(d => {
          const el = document.getElementById('cartCount');
          if (el && d.count !== undefined) el.textContent = d.count;
        });
    } else if (data.status === 'ya_existe') {
      alert('Ya está en el carrito.');
    } else if (data.error) {
      alert('Error: ' + data.error);
    }
  })
  .catch(() => {
    alert('Error de conexión.');
  });
}

// ──────────────────────────────────────────────────────────────────────
// 2) ME GUSTA
// ──────────────────────────────────────────────────────────────────────
function toggleLike(cardElement) {
  const contenidoId = cardElement.getAttribute('data-id');
  const btn = cardElement.querySelector('.like-btn');
  const countSpan = btn.querySelector('.likes-count');

  fetch('toggle_like.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'contenido_id=' + encodeURIComponent(contenidoId)
  })
  .then(res => res.json())
  .then(data => {
    if (data.error) {
      console.error(data.error);
      return;
    }
    // Actualiza recuento y estado visual
    countSpan.textContent = data.count;
    if (data.status === 'liked') {
      btn.classList.add('liked');
    } else {
      btn.classList.remove('liked');
    }
  })
  .catch(err => {
    console.error('Error al cambiar like:', err);
  });
}

document.addEventListener('DOMContentLoaded', () => {
  // 2.1) Obtener recuentos iniciales de “likes” para cada tarjeta
  document.querySelectorAll('#likesGrid .card').forEach(card => {
    const contenidoId = card.getAttribute('data-id');
    const countSpan = card.querySelector('.likes-count');
    fetch('get_likes_count.php?contenido_id=' + encodeURIComponent(contenidoId))
      .then(res => res.json())
      .then(data => {
        if (!data.error) {
          countSpan.textContent = data.count;
        }
      })
      .catch(err => console.error('Error al leer recuento de likes:', err));
  });

  // 2.2) Asignar evento “click” a cada botón “like-btn”
  document.querySelectorAll('#likesGrid .like-btn').forEach(btn => {
    const card = btn.closest('.card');
    btn.addEventListener('click', () => {
      toggleLike(card);
    });
  });
});
</script>

<?php require 'footer.php'; ?>
