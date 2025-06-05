<?php
// cart.php
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

// obtener ID de usuario
$stmt = $pdo->prepare("SELECT id_usuarios FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$user]);
$id_usuario = $stmt->fetchColumn();

// obtener artículos del carrito
$stmt = $pdo->prepare("
  SELECT c.id_contenido, c.titulo, c.descripcion, c.tipo_mime
  FROM carrito ca
  JOIN contenidos c ON ca.id_contenido = c.id_contenido
  WHERE ca.id_usuario = ?
");
$stmt->execute([$id_usuario]);
$items = $stmt->fetchAll();
?>
<link rel="stylesheet" href="css/cart.css">
<link
  href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
  rel="stylesheet"
/>
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2>Mi Carrito</h2>
      <div class="underline"></div>
    </div>

    <div class="cart-container">
      <div class="section-description">
        <p>Gestiona aquí tus elementos favoritos y descargas pendientes del catálogo.</p>
      </div>

      <div class="cart-header">
        <h3>Elementos en el carrito</h3>

        <div class="buttons">
          <form method="post" action="clear_cart.php"
                onsubmit="return confirm('¿Vaciar todo el carrito?');"
                style="display:inline;">
            <button class="btn-secondary">
              <i class="fas fa-trash"></i> Vaciar carrito
            </button>
          </form>
          <form method="post" action="download_all.php"          
            onsubmit="return confirm('¿Descargar todo el carrito?');"
            style="display:inline">
            <button class="btn-secondary">
                <i class="fas fa-download"></i> Descargar todo
            </button>
          </form>
        </div>
      </div>

      <ul class="cart-items">
        <?php if (empty($items)): ?>
          <div id="empty-cart" class="cart-empty">
            <i class="fas fa-shopping-cart fa-3x" style="color: var(--primary-color);"></i>
            <h3>Tu carrito está vacío</h3>
            <p>Explora el catálogo para agregar contenido</p>
            <a href="catalog.php" class="btn-primary">
              <i class="fas fa-search"></i> Ir al Catálogo
            </a>
          </div>
        <?php else: ?>
          <?php foreach ($items as $item): ?>
            <li class="cart-item">
              <div>
                <strong><?= htmlspecialchars($item['titulo']) ?></strong><br>
                <small><?= htmlspecialchars($item['descripcion']) ?></small>
              </div>
              <div style="margin-left:auto; display:flex; gap:10px;">
                <a class="btn-primary"
                   href="download.php?id=<?= $item['id_contenido'] ?>">
                  <i class="fas fa-download"></i> Descargar
                </a>
                <form method="post" action="remove_from_cart.php">
                  <input type="hidden" name="id_contenido" value="<?= $item['id_contenido'] ?>">
                  <button class="btn-secondary" type="submit">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </form>
              </div>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</section>

<?php require 'footer.php'; ?>
