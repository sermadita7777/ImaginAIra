<?php
// profile.php
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

// 1. Obtener fecha de registro
$stmt = $pdo->prepare("SELECT created_at FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$user]);
$row = $stmt->fetch();
if ($row) {
  $dt = new DateTime($row['created_at']);
  $memberSince = $dt->format('d/m/Y');
} else {
  $memberSince = '—';
}

// 2. Obtener id_usuario
$stmt2 = $pdo->prepare("SELECT id_usuarios FROM usuarios WHERE nombre_usuario = ?");
$stmt2->execute([$user]);
$id_usuario = $stmt2->fetchColumn();

// 3. Contar cuántos elementos hay en carrito
$countCarrito = 0;
if ($id_usuario) {
  $stmt3 = $pdo->prepare("SELECT COUNT(*) FROM carrito WHERE id_usuario = ?");
  $stmt3->execute([$id_usuario]);
  $countCarrito = (int) $stmt3->fetchColumn();
}

// 4. Obtener publicaciones del usuario
$posts = [];
if ($id_usuario) {
  $stmt4 = $pdo->prepare("SELECT * FROM contenidos WHERE usuario_id = ? ORDER BY created_at DESC");
  $stmt4->execute([$id_usuario]);
  $posts = $stmt4->fetchAll(PDO::FETCH_ASSOC);
}

// Función para truncar texto
function truncar_texto($texto, $max) {
  return strlen($texto) > $max ? substr($texto, 0, $max) . '...' : $texto;
}
?>

<link rel="stylesheet" href="css/profile.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

<section class="section">
  <div class="container">
    <div class="section-header">
      <h2>Mi Perfil</h2>
      <div class="underline"></div>
    </div>

    <div class="profile-container">
      <div class="profile-header">
        <div class="profile-avatar"><i class="fas fa-user"></i></div>
        <div class="profile-info">
          <h3>Bienvenido, <?= htmlspecialchars($user) ?></h3>
          <p>Miembro desde: <?= $memberSince ?></p>
        </div>
      </div>

      <div class="profile-stats">
        <a href="likes.php" class="btnStat">
          <div class="stat-card">
            <i class="fas fa-heart" style="color: var(--accent-color)"></i>
            <h4>Favoritos</h4>
            <p id="favCount">0</p>
          </div>
        </a>
        <a href="cart.php" class="btnStat">
          <div class="stat-card">
            <i class="fas fa-shopping-cart" style="color: var(--primary-color)"></i>
            <h4>En Carrito</h4>
            <p id="cartItemCount"><?= $countCarrito ?></p>
          </div>
        </a>
      </div>

      <div class="profile-actions">
        <a href="upload.php" class="btn-primary">
          <i class="fas fa-upload"></i> ¡Publica algo!
        </a>
        <a href="profile_settings.php" class="btn-primary">
          <i class="fas fa-cog"></i> Gestiona tu perfil
        </a>         
        <form method="post" action="logout.php" style="display:inline;">
          <button type="submit" class="btn-secondary">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
          </button>
        </form>
      </div>
    </div>

    <div class="misPublicaciones">
      <div class="section-header">
        <h2>Mis Publicaciones</h2>
        <div class="underline"></div>
      </div>

      <?php if (empty($posts)): ?>
        <p style="text-align: center;">Aún no has subido ninguna publicación.</p>
      <?php else: ?>
        <div class="card-grid">
          <?php foreach ($posts as $post): ?>
            <div class="card-body">
              <h3><?= htmlspecialchars($post['titulo']) ?></h3>
              <p class="card-author">
                Publicado por: <strong><?= htmlspecialchars($user) ?></strong>
              </p>

              <?php if (!empty($post['generos'])): 
                $arrGeneros = explode(',', $post['generos']);
              ?>
                <div class="genre-tags">
                  <?php foreach ($arrGeneros as $g): ?>
                    <span class="genre-tag"><?= htmlspecialchars($g) ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <p class="card-desc">
                <?= nl2br(htmlspecialchars(truncar_texto($post['descripcion'], 100))) ?>
              </p>

              <div class="card-actions">
                <button class="like-btn">
                  <i class="fas fa-heart"></i>
                  <span class="likes-count"><?= $post['likes'] ?? 0 ?></span>
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<script src="js/profile.js" defer></script>
 
<?php require 'footer.php'; ?>
