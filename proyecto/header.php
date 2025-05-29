<?php
// header.php — Incluye sesión y navegación dinámica
session_start();
require 'db.php';
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/index.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/cookies.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="js/index.js" defer></script>
  <script src="js/cookies.js" defer></script>
  <title>IA-Lovers</title>
</head>
<body>

<!-- Añade esto justo después de la etiqueta <body> -->
<div id="cookie-overlay" class="cookie-overlay hidden"></div>

<div id="cookie-banner" class="cookie-banner hidden"> 
  <div class="cookie-content"> 
    <h3>Usamos cookies</h3> 
    <p> 
      Utilizamos cookies propias y de terceros para analizar el uso de la web y personalizar contenido. 
      Puedes aceptar todas las cookies o configurar tus preferencias. 
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
      <label><input type="checkbox" name="necessary" checked disabled> Cookies necesarias (obligatorias)</label><br> 
      <label><input type="checkbox" name="analytics"> Cookies analíticas</label><br> 
      <label><input type="checkbox" name="personalization"> Cookies de personalización</label><br> 
      <label><input type="checkbox" name="marketing"> Cookies de marketing</label><br> 
      <button type="submit" class="cookie-btn save">Guardar preferencias</button> 
    </form> 
  </div> 
</div>

<nav class="navbar">
  <div class="container">
    <div class="logo">
      <img src="imagenes/logo.png" alt="IA-Lovers Logo">
      <span>IA-<span class="highlight">Lovers</span></span>
    </div>
    <ul class="nav-links">
      <li><a href="index.php">Inicio</a></li>
      <li><a href="catalog.php">Catálogo</a></li>

      <?php if($user): ?>
        <!-- Carrito -->
        <li>
          <a href="cart.php">
            <i class="fas fa-shopping-cart"></i>
            <span id="cartCount">0</span>
          </a>
        </li>
        <!-- Perfil -->
        <li>
          <a href="profile.php">
            <i class="fas fa-user"></i> <?=htmlspecialchars($user)?>
          </a>
        </li>
        <!-- Cerrar sesión -->
        <li><a href="logout.php" class="login-btn">Cerrar Sesión</a></li>
      <?php else: ?>
        <!-- Enlaces para usuarios no autenticados -->
        <li><a href="login.php" class="login-btn">Iniciar Sesión</a></li>
        <li><a href="register.php" class="register-btn">Regístrate</a></li>
      <?php endif; ?>

    </ul>
    <div class="hamburger"><span></span><span></span><span></span></div>
  </div>
</nav>
