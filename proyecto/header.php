<?php
session_start();
require 'db.php';
$user = $_SESSION['user'] ?? null;
$rol = null;

if ($user) {
    $stmt = $pdo->prepare("SELECT rol FROM usuarios WHERE nombre_usuario = ?");
    $stmt->execute([$user]);
    $rol = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" href="imagenes/logo.png" type="image/x-icon">
  <link rel="stylesheet" href="css/nav.css" />
  <link rel="stylesheet" href="css/index.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/cookies.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="js/cookies.js" defer></script>
  <script src="js/nav.js" defer></script>
  <title>IA-Lovers</title>

  <style>
    #notificaciones-lista {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.35s ease-in-out, opacity 0.35s ease-in-out;
      opacity: 0;
      position: absolute;
      top: 35px;
      right: 0;
      width: 320px;
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
      font-family: 'Segoe UI', sans-serif;
      z-index: 1000;
    }

    #notificaciones-lista.open {
      max-height: 400px;
      opacity: 1;
    }

    #notificaciones-lista > p:first-child {
      padding: 12px 16px;
      margin: 0;
      font-weight: bold;
      border-bottom: 1px solid #eee;
      background-color: #f9f9f9;
      font-size: 15px;
    }

    #notif-empty {
      padding: 16px;
      text-align: center;
      color: #888;
      font-size: 14px;
    }

    .notif-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 16px;
      border-bottom: 1px solid #f0f0f0;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    .notif-item:hover {
      background-color: #f3f7ff;
    }

    .notif-item.unread {
      background-color: #eef6ff;
    }

    .notif-item i {
      color: #007bff;
      font-size: 18px;
    }

    .notif-item .text {
      flex: 1;
      font-size: 14px;
      color: #333;
    }

    .notif-item .text .time {
      font-size: 12px;
      color: #999;
      margin-top: 4px;
    }
  </style>
</head>
<body>

<!-- Overlay y banner de cookies -->
<div id="cookie-overlay" class="cookie-overlay hidden"></div>
<div id="cookie-banner" class="cookie-banner hidden">
  <div class="cookie-content">
    <h3>Usamos cookies</h3>
    <p>
      Utilizamos cookies propias y de terceros para analizar el uso de la web y personalizar contenido.
      Consulta nuestra <a href="politica-cookies.html" target="_blank">Política de Cookies</a>.
    </p>
    <div class="cookie-buttons">
      <button id="acceptAllCookies" class="cookie-btn accept">Aceptar todas</button>
      <button id="customizeCookies" class="cookie-btn settings">Configurar</button>
    </div>
  </div>

  <div id="cookie-settings" class="cookie-settings hidden">
    <h4>Preferencias de cookies</h4>
    <form id="cookieForm">
      <label><input type="checkbox" name="necessary" checked disabled> Cookies necesarias</label><br>
      <label><input type="checkbox" name="analytics"> Cookies analíticas</label><br>
      <label><input type="checkbox" name="personalization"> Cookies de personalización</label><br>
      <label><input type="checkbox" name="marketing"> Cookies de marketing</label><br>
      <button type="submit" class="cookie-btn save">Guardar preferencias</button>
    </form>
  </div>
</div>

<nav class="navbar">
  <div class="container">
    <a href="index.php" class="logo">
      <img src="imagenes/logo.png" alt="IA-Lovers Logo" />
      <span>IA-<span class="highlight">Lovers</span></span>
    </a>
    <ul class="nav-links">
      <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Inicio</a></li>
      <li><a href="catalog.php" class="<?= basename($_SERVER['PHP_SELF']) === 'catalog.php' ? 'active' : '' ?>">Catálogo</a></li>

      <?php if ($user): ?>
        <?php if ($rol === 'admin'): ?>
          <li><a href="admin.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin.php' ? 'active' : '' ?>">Panel Admin</a></li>
        <?php endif; ?>

        <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> <span id="cartCount">0</span></a></li>
        <li><a href="profile.php"><i class="fas fa-user"></i> <?= htmlspecialchars($user) ?></a></li>
        <li><a href="logout.php" class="login-btn">Cerrar Sesión</a></li>

        <li class="nav-notifications" style="position: relative;">
          <button id="notif-btn" style="background:none; border:none; cursor:pointer; position: relative;">
            <i class="fas fa-bell"></i>
            <span id="notif-count" style="position: absolute; top: -6px; right: -6px; background: #c00; color: #fff; font-size: 12px; padding: 2px 6px; border-radius: 50%; display: none;"></span>
          </button>
          <div id="notificaciones-lista">
            <p>Notificaciones</p>
            <div id="notif-items"></div>
            <p id="notif-empty" style="display: none;">No tienes notificaciones.</p>
          </div>
        </li>
      <?php else: ?>
        <li><a href="login.php" class="login-btn">Iniciar Sesión</a></li>
        <li><a href="register.php" class="register-btn">Regístrate</a></li>
      <?php endif; ?>
    </ul>
    <div class="hamburger"><span></span><span></span><span></span></div>
  </div>
</nav>

<?php if ($user): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Carrito
  fetch('cart_count.php')
    .then(res => res.json())
    .then(data => {
      const el = document.getElementById('cartCount');
      if (el && data.count !== undefined) {
        el.textContent = data.count;
      }
    });

  // Notificaciones
  const notifBtn = document.getElementById('notif-btn');
  const notifList = document.getElementById('notificaciones-lista');
  const notifItems = document.getElementById('notif-items');
  const notifCount = document.getElementById('notif-count');
  const notifEmpty = document.getElementById('notif-empty');

  async function cargarNotificaciones() {
    try {
      const res = await fetch('get_notifications.php');
      const data = await res.json();
      notifItems.innerHTML = '';
      let sinLeer = 0;

      if (!data.notificaciones || data.notificaciones.length === 0) {
        notifEmpty.style.display = 'block';
        notifCount.style.display = 'none';
        return;
      }

      notifEmpty.style.display = 'none';

      data.notificaciones.forEach(n => {
        if (!n.leida) sinLeer++;

        const div = document.createElement('div');
        div.className = 'notif-item' + (n.leida ? '' : ' unread');
        div.innerHTML = `
          <i class="fas fa-heart"></i>
          <div class="text">
            <div>${n.mensaje}</div>
            <div class="time">${new Date(n.fecha).toLocaleString()}</div>
          </div>
        `;
        div.dataset.id = n.id;
        div.dataset.leida = n.leida;

        div.addEventListener('click', async () => {
          if (div.dataset.leida === '0') {
            const res = await fetch('mark_notification_read.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: 'id=' + encodeURIComponent(div.dataset.id)
            });
            const result = await res.json();
            if (result.status === 'ok') {
              div.classList.remove('unread');
              div.dataset.leida = '1';
              sinLeer--;
              actualizarContador();
            }
          }
        });

        notifItems.appendChild(div);
      });

      actualizarContador();

      function actualizarContador() {
        if (sinLeer > 0) {
          notifCount.style.display = 'inline-block';
          notifCount.textContent = sinLeer;
        } else {
          notifCount.style.display = 'none';
        }
      }
    } catch (error) {
      console.error('Error al cargar notificaciones:', error);
    }
  }

  notifBtn.addEventListener('click', () => {
    notifList.classList.toggle('open');
  });

  document.addEventListener('click', (e) => {
    if (!notifBtn.contains(e.target) && !notifList.contains(e.target)) {
      notifList.classList.remove('open');
    }
  });

  cargarNotificaciones();
  setInterval(cargarNotificaciones, 60000);
});
</script>
<?php endif; ?>
