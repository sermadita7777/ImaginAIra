<?php
session_start();
require 'db.php';

$user = $_SESSION['user'] ?? null;
$mostrarAdmin = false;

if ($user) {
    // Obtener rol del usuario logueado
    $stmt = $pdo->prepare("SELECT rol FROM usuarios WHERE nombre_usuario = ?");
    $stmt->execute([$user]);
    $rol = $stmt->fetchColumn();
    if ($rol === 'admin') {
        $mostrarAdmin = true;
    }
}
?>

<footer class="footer">
  <div class="container">
    <div class="footer-content">
      <div class="footer-logo">
        <span>IA-<span class="highlight">Lovers</span></span>
        <p>Tu plataforma para contenido generado por IA</p>
      </div>
      <div class="footer-links">
        <div class="footer-column">
          <h4>Navegación</h4>
          <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="catalog.php">Catálogo</a></li>
            <?php if ($mostrarAdmin): ?>
              <li><a href="admin.php">Administración</a></li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="footer-column">
          <h4>Legal</h4>
          <ul>
            <li><a href="#">Términos de Uso</a></li>
            <li><a href="#">Política de Privacidad</a></li>
            <li><a href="politica-cookies.html">Cookies</a></li>
          </ul>
        </div>
        <div class="footer-column">
          <h4>Contacto</h4>
          <ul>
            <li><a href="#"><i class="fas fa-envelope"></i> contacto@ialovers.com</a></li>
            <li><a href="#"><i class="fas fa-phone"></i> +34 123 456 789</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> IA-Lovers. Todos los derechos reservados.</p>
    </div>
  </div>
</footer>
</body>
</html>
