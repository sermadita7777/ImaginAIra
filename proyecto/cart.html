<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Carrito - IA-Lovers</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <header class="header">
    <div class="container header__inner">
      <a href="index.html">← Volver</a>
      <button class="btn-outline" onclick="logoutUser()">Cerrar sesión</button>
    </div>
  </header>
  <main class="container" style="margin:80px auto; max-width:800px;">
    <h2>Mi Carrito</h2>
    <ul id="cartList" class="grid" style="list-style:none;padding:0;"></ul>
    <button id="downloadBtn" class="btn-solid" style="margin-top:24px;">Descargar Todo</button>
  </main>

  <script src="js/script.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const u = localStorage.getItem('currentUser');
      if (!u) return location.href = 'login.html';
      const cart = JSON.parse(localStorage.getItem('iaCart')||'[]');
      const ul = document.getElementById('cartList');
      if (!cart.length) ul.innerHTML = '<p>El carrito está vacío.</p>';
      cart.forEach(item => {
        const li = document.createElement('li');
        li.textContent = item;
        ul.appendChild(li);
      });
      document.getElementById('downloadBtn').addEventListener('click', () => {
        const blob = new Blob([JSON.stringify(cart, null,2)], {type:'application/json'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url; a.download = 'ia_lovers_carrito.json';
        a.click(); URL.revokeObjectURL(url);
      });
    });
  </script>