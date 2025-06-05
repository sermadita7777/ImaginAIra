<?php
// catalog.php
require 'header.php';

// 1) Recuperar todo el contenido (con autor y géneros para Literatura)
$stmt = $pdo->query("
  SELECT 
    c.id_contenido, 
    c.tipo, 
    c.titulo, 
    c.descripcion, 
    c.tipo_mime,
    -- Subconsulta que devuelve las etiquetas (géneros) concatenadas
    (
      SELECT GROUP_CONCAT(e.nombre SEPARATOR ',')
      FROM contenido_etiquetas ce
      JOIN etiquetas e ON ce.etiqueta_id = e.id
      WHERE ce.contenido_id = c.id_contenido
    ) AS generos,
    -- Saber si existe miniatura
    (c.miniatura_blob IS NOT NULL) AS tiene_miniatura,
    u.nombre_usuario AS autor
  FROM contenidos c
  JOIN usuarios u ON c.usuario_id = u.id_usuarios
  ORDER BY c.id_contenido DESC
");
$all = $stmt->fetchAll();

// 2) Agrupar por tipo
$catalog = [
  'memes'      => [],
  'arte'       => [],
  'literatura' => []
];
foreach ($all as $item) {
  $catalog[$item['tipo']][] = $item;
}

// 3) Función para truncar texto a N caracteres (añade “…”)
function truncar_texto(string $texto, int $max_chars = 100): string {
    if (mb_strlen($texto) <= $max_chars) {
        return $texto;
    }
    return mb_substr($texto, 0, $max_chars) . '…';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="css/catalog.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet"
  />
  <title>Catálogo | IA-Lovers</title>
</head>
<body>
  <!-- BARRA DE BÚSQUEDA -->
  <section class="search-section">
    <div class="container">
      <input
        type="text"
        id="searchInput"
        placeholder="Buscar contenido..."
        class="search-bar"
      />
    </div>
  </section>

  <!-- PESTAÑAS -->
  <section class="section-tabs">
    <div class="container">
      <button class="tab-btn active" data-cat="memes">Memes</button>
      <button class="tab-btn" data-cat="arte">Arte</button>
      <button class="tab-btn" data-cat="literatura">Literatura</button>
    </div>
  </section>

  <!-- SECCIONES DEL CATÁLOGO -->
  <?php foreach (['memes','arte','literatura'] as $cat): ?>
    <section id="<?= $cat ?>"
             class="catalog <?= $cat === 'memes' ? 'active' : '' ?>">
      <div class="container">

        <?php if ($cat === 'literatura'): ?>
          <!-- FILTROS DE LITERATURA -->
          <div class="lit-filters">
            <span>Filtrar por género:</span>
            <!-- Nota: sin opción “ficcion” aislada, ya que existe “ciencia-ficcion” -->
            <?php
              $posibles = ['ciencia-ficcion','ensayo','fantasia','misterio','poesia','romance','terror'];
              foreach ($posibles as $gen):
            ?>
              <label>
                <input type="checkbox" class="filter-checkbox" value="<?= $gen ?>" />
                <span><?= ucfirst(str_replace('-', ' ', $gen)) ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="grid">
          <?php if (empty($catalog[$cat])): ?>
            <p class="no-items">No hay <?= ucfirst($cat) ?> aún.</p>
          <?php else: ?>
            <?php foreach ($catalog[$cat] as $post): ?>
              <div class="card" data-id="<?= $post['id_contenido'] ?>"
                   <?php if ($cat === 'literatura' && !empty($post['generos'])): 
                          $arrGeneros = explode(',', $post['generos']);
                          // Para filtrado JS, guardamos géneros en data-tags
                          $dataTags = htmlspecialchars(json_encode($arrGeneros), ENT_QUOTES, 'UTF-8');
                        ?>
                data-tags='<?= $dataTags ?>'
                   <?php endif; ?>
              >
                <?php
                  // SI ES PDF:
                  if ($post['tipo_mime'] === 'application/pdf'):
                    // Si tiene miniatura (blob), la mostramos
                    if ($post['tiene_miniatura']): ?>
                      <img
                        src="thumbnail.php?id_contenido=<?= $post['id_contenido'] ?>&thumb=1"
                        alt="Miniatura de <?= htmlspecialchars($post['titulo']) ?>"
                        class="card-img"
                      />
                    <?php else: ?>
                      <i class="fas fa-file-pdf pdf-icon"></i>
                    <?php endif;
                  else:
                    // SI ES IMAGEN (JPG/PNG), mostrar la imagen completa escalada
                    ?>
                    <img
                      src="thumbnail.php?id_contenido=<?= $post['id_contenido'] ?>"
                      alt="<?= htmlspecialchars($post['titulo']) ?>"
                      class="card-img"
                    />
                <?php endif; ?>

                <div class="card-body">
                  <!-- TÍTULO -->
                  <h3><?= htmlspecialchars($post['titulo']) ?></h3>
                  <!-- AUTOR -->
                  <p class="card-author">
                    Publicado por: <strong><?= htmlspecialchars($post['autor']) ?></strong>
                  </p>

                  <!-- PARA LITERATURA: mostrar los géneros como “pills” -->
                  <?php if ($cat === 'literatura' && !empty($post['generos'])): 
                    $arrGeneros = explode(',', $post['generos']);
                  ?>
                    <div class="genre-tags">
                      <?php foreach ($arrGeneros as $g): ?>
                        <span class="genre-tag"><?= htmlspecialchars($g) ?></span>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>

                  <!-- DESCRIPCIÓN TRUNCADA A 100 CARACTERES -->
                  <p class="card-desc">
                    <?= nl2br(htmlspecialchars(truncar_texto($post['descripcion'], 100))) ?>
                  </p>

                  <div class="card-actions">
                    <button class="like-btn">
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
    </section>
  <?php endforeach; ?>

  <?php require 'footer.php'; ?>

  <!-- JAVASCRIPT: Carrito, Me Gusta, Filtros y Pestañas -->
  <script>
    /* ────────────────────────────────────────────────────────────────────── */
    /* 1) CARRITO                                                           */
    /* ────────────────────────────────────────────────────────────────────── */
    function addToCart(id) {
      fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_contenido=' + encodeURIComponent(id)
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'ok') {
          // Actualiza contador de cabecera
          fetch('cart_count.php')
            .then(res => res.json())
            .then(d => {
              const el = document.getElementById('cartCount');
              if (el && d.count !== undefined) el.textContent = d.count;
            });
        } else if (data.status === 'ya_existe') {
          // nada más
        } else if (data.error) {
          console.error(data.error);
        }
      })
      .catch(() => {
        console.error('Error de conexión al añadir al carrito.');
      });
    }

    /* ────────────────────────────────────────────────────────────────────── */
    /* 2) ME GUSTA                                                           */
    /* ────────────────────────────────────────────────────────────────────── */
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
      // 2.1) Marcar likes existentes del usuario
      fetch('get_user_likes.php')
        .then(res => res.json())
        .then(userLikesArray => {
          document.querySelectorAll('.card').forEach(card => {
            const contenidoId = parseInt(card.getAttribute('data-id'), 10);
            const btn = card.querySelector('.like-btn');
            if (userLikesArray.includes(contenidoId)) {
              btn.classList.add('liked');
            }
          });
        })
        .catch(err => console.error('No se pudo obtener likes:', err));

      // 2.2) Obtener recuento de likes para cada tarjeta
      document.querySelectorAll('.card').forEach(card => {
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

      // 2.3) Evento “click” en cada botón de like
      document.querySelectorAll('.like-btn').forEach(btn => {
        const card = btn.closest('.card');
        btn.addEventListener('click', () => {
          toggleLike(card);
        });
      });

      /* ──────────────────────────────────────────────────────────────────── */
      /* 3) PESTAÑAS + BÚSQUEDA                                               */
      /* ──────────────────────────────────────────────────────────────────── */
      document.querySelectorAll('.tab-btn').forEach(tab => {
        tab.addEventListener('click', () => {
          document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
          tab.classList.add('active');
          document.querySelectorAll('.catalog').forEach(sec => {
            sec.classList.toggle('active', sec.id === tab.dataset.cat);
          });
        });
      });

      document.getElementById('searchInput').addEventListener('input', e => {
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('.card').forEach(c => {
          const titulo = c.querySelector('h3').textContent.toLowerCase();
          c.style.display = titulo.includes(q) ? '' : 'none';
        });
      });

      /* ──────────────────────────────────────────────────────────────────── */
      /* 4) FILTROS LITERATURA                                                */
      /* ──────────────────────────────────────────────────────────────────── */
      const checkboxes = document.querySelectorAll('.filter-checkbox');
      checkboxes.forEach(cb => {
        cb.addEventListener('change', applyFilters);
      });

      function applyFilters() {
        // Array de géneros seleccionados
        const seleccionados = Array.from(checkboxes)
          .filter(c => c.checked)
          .map(c => c.value);

        document.querySelectorAll('#literatura .card').forEach(card => {
          const tags = JSON.parse(card.getAttribute('data-tags') || '[]');
          if (seleccionados.length === 0) {
            // Si no hay filtros seleccionados, mostrar todo
            card.style.display = '';
          } else {
            // Mostrar sólo si tiene al menos uno de los géneros seleccionados
            const intersecta = tags.some(t => seleccionados.includes(t));
            card.style.display = intersecta ? '' : 'none';
          }
        });
      }
    });
  </script>
</body>
</html>
