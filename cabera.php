<?php
// includes/cabecera.php
require_once __DIR__ . '/auth.php';
?>
<header class="header">
  <div class="container header__inner">

    <!-- Logo -->
    <a href="/" class="logo">
      <img src="/imagenes/logo.png" alt="IA-Lovers Logo">
      <span>IA-Lovers</span>
    </a>

    <!-- Buscador -->
    <input id="searchInput" type="text" placeholder="Buscar contenido…">

    <!-- MENÚ INVITADO: se oculta si estaLogueado() es true -->
    <nav id="authNav" class="<?= estaLogueado() ? 'hidden' : '' ?>">
      <a href="/login/login.php"  class="btn-outline">Iniciar Sesión</a>
      <a href="/login/register.php" class="btn-solid">Registro</a>
    </nav>

    <!-- MENÚ USUARIO: se muestra si estaLogueado() es true -->
    <nav id="userNav" class="<?= estaLogueado() ? '' : 'hidden' ?>">
      <?php if (estaLogueado()):
        require_once __DIR__ . '/bd.php';
        $u = obtenerUsuarioPorId($_SESSION['usuario_id']);
        // Usamos avatar.php para leer de storage/avatars/
        $avatarUrl = '/avatar.php';
      ?>
        <a href="/profile.php" class="user-link">
          <img
            src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES) ?>"
            alt="Avatar de <?= htmlspecialchars($u['username'], ENT_QUOTES) ?>"
            class="avatar"
              style="width:40px;height:40px;object-fit:cover;border-radius:50%;"
            alt="Avatar"
          >
          <span><?= htmlspecialchars($u['username'], ENT_QUOTES) ?></span>
        </a>
        <a href="/cart.html" class="icon">
          🛒<span id="cartCount">0</span>
        </a>
        <a href="/cerrar_sesion.php" class="btn-solid">Cerrar Sesión</a>
      <?php endif; ?>
    </nav>

  </div>
</header>
