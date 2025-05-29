<?php
// profile.php
require 'header.php';
if (!$user) {
  header('Location: login.php');
  exit;
}

// Obtenemos fecha de creación en dd/mm/YYYY
$stmt = $pdo->prepare("SELECT created_at FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$user]);
$row = $stmt->fetch();
if ($row) {
  $dt = new DateTime($row['created_at']);
  $memberSince = $dt->format('d/m/Y');
} else {
  $memberSince = '—';
}
?>
<link rel="stylesheet" href="css/profile.css">

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
            <i class="fas fa-heart" style="color: var(--accent-color);"></i>
            <h4>Favoritos</h4>
            <p id="favCount">0</p>
          </div>
        </a>
        <a href="cart.php" class="btnStat">
          <div class="stat-card">
            <i class="fas fa-shopping-cart" style="color: var(--primary-color);"></i>
            <h4>En Carrito</h4>
            <p id="cartItemCount">0</p>
          </div>
        </a>
      </div>

      <div class="profile-actions">
        <a href="upload.php" class="btn-primary">
          <i class="fas fa-upload"></i> ¡Publica algo!
        </a>
        <form method="post" action="logout.php" style="display:inline;">
          <button type="submit" class="btn-secondary">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
          </button>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Script para actualizar contadores -->
<script src="js/profile.js" defer></script>
<?php require 'footer.php'; ?>
