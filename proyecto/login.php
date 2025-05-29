<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']);
    $password   = $_POST['password'];

    // Busca por usuario o email
    $stmt = $pdo->prepare("
        SELECT nombre_usuario, password 
        FROM usuarios 
        WHERE nombre_usuario = ? OR email = ?
    ");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Login exitoso
        $_SESSION['user'] = $user['nombre_usuario'];
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['error'] = 'Usuario/correo o contraseña incorrectos.';
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión | IA-Lovers</title>
  <link rel="stylesheet" href="css/index.css">
  <link rel="stylesheet" href="css/auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<main class="auth-container">
  <div class="auth-card">
    <h2>Iniciar Sesión</h2>
    <?php if (!empty($_SESSION['error'])): ?>
      <div class="error-msg">
        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>
    <form method="post" class="auth-form">
      <div class="form-group">
        <label for="loginIdentifier">Usuario o Correo</label>
        <input type="text" id="loginIdentifier" name="identifier" required autofocus>
      </div>
      <div class="form-group">
        <label for="loginPass">Contraseña</label>
        <input type="password" id="loginPass" name="password" required>
      </div>
      <button type="submit">Entrar</button>
    </form>
    <p class="auth-footer">
      ¿No tienes cuenta? <a href="register.php">Regístrate</a>
    </p>
  </div>
</main>
</body>
</html>
