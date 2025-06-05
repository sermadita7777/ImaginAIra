<?php
// register.php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Validaciones
    if (strlen($username) < 3) {
        $_SESSION['error'] = 'El nombre de usuario debe tener al menos 3 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'El correo electrónico no es válido.';
    } elseif (strlen($password) < 5) {
        $_SESSION['error'] = 'La contraseña debe tener al menos 5 caracteres.';
    } else {
        // Comprobar duplicados
        $stmt = $pdo->prepare("
            SELECT id_usuarios 
              FROM usuarios 
             WHERE nombre_usuario = ? OR email = ?
            LIMIT 1
        ");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'El nombre de usuario o correo ya está en uso.';
        } else {
            // Insertar usuario
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO usuarios (nombre_usuario, email, password)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$username, $email, $hash]);
            // Recuperar su ID
            $userId = $pdo->lastInsertId();
            // Guardar en sesión
            $_SESSION['user']    = $username;
            $_SESSION['user_id'] = $userId;
            header('Location: index.php');
            exit;
        }
    }
    // Hay error: redirigir
    header('Location: register.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro | IA-Lovers</title>
  <link rel="stylesheet" href="css/index.css">
  <link rel="stylesheet" href="css/auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<main class="auth-container">
  <div class="auth-card">
    <h2>Crear Cuenta</h2>
    <?php if (!empty($_SESSION['error'])): ?>
      <div class="error-msg">
        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>
    <form method="post" class="auth-form">
      <div class="form-group">
        <label for="regUser">Nombre de Usuario</label>
        <input type="text" id="regUser" name="username" required autofocus>
      </div>
      <div class="form-group">
        <label for="regEmail">Correo Electrónico</label>
        <input type="email" id="regEmail" name="email" required>
      </div>
      <div class="form-group">
        <label for="regPass">Contraseña</label>
        <input type="password" id="regPass" name="password" required>
      </div>
      <button type="submit">Crear Cuenta</button>
    </form>
    <p class="auth-footer">
      ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
    </p>
  </div>
</main>
</body>
</html>
