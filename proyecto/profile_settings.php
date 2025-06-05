<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$user]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoUser= $_POST['new_user'] ?? '';
    $nuevoCorreo = $_POST['correo'] ?? '';
    $nuevaPassword = $_POST['nueva_password'] ?? '';
    $confirmarPassword = $_POST['confirmar_password'] ?? '';

    $errores = [];

    if (!empty($nuevoUser) && $nuevoUser == $usuario['nombre_usuario']) {
        $errores[] = "El nombre de usuario no puede ser igual al anterior.";
    }

    if (!empty($nuevoCorreo) && !filter_var($nuevoCorreo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Correo inválido.";
    }

    if (!empty($nuevaPassword) && $nuevaPassword !== $confirmarPassword) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (empty($errores)) {

        if (!empty($nuevoUser) && $nuevoUser != $usuario['nombre_usuario']) {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre_usuario = ? WHERE nombre_usuario = ?");
            $stmt->execute([$nuevoUser, $user]);
            $_SESSION['user'] = $nuevoUser;
            $user = $nuevoUser;
        }

        if (!empty($nuevoCorreo)) {
            $stmt = $pdo->prepare("UPDATE usuarios SET email = ? WHERE nombre_usuario = ?");
            $stmt->execute([$nuevoCorreo, $user]);
        }

        if (!empty($nuevaPassword)) {
            $hashedPassword = password_hash($nuevaPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE nombre_usuario = ?");
            $stmt->execute([$hashedPassword, $user]);
        }

        $mensajeExito = "Perfil actualizado correctamente.";

        // Refrescar datos
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ?");
        $stmt->execute([$user]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de Perfil</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .profile-settings {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .profile-settings h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .profile-settings label {
            display: block;
            margin-top: 15px;
        }

        .profile-settings input[type="text"],
        .profile-settings input[type="email"],
        .profile-settings input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .profile-settings button {
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .profile-settings button:hover {
            background: #0056b3;
        }

        .mensaje {
            margin-top: 15px;
            padding: 10px;
            background: #e0ffe0;
            color: #006600;
            border: 1px solid #a6d8a6;
            border-radius: 5px;
        }

        .errores {
            margin-top: 15px;
            background: #ffe0e0;
            color: #b30000;
            padding: 10px;
            border: 1px solid #dba6a6;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="profile-settings">
    <h2>Configuración de Perfil</h2>

    <?php if (!empty($mensajeExito)): ?>
        <div class="mensaje"><?= $mensajeExito ?></div>
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
        <div class="errores">
            <ul>
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Nombre de usuario:</label>
        <strong><?= htmlspecialchars($usuario['nombre_usuario']) ?></strong>

        <label>Nuevo nombre de usuario:</label>
        <input type="text" name="new_user">
            
        <label>Correo electrónico:</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo'] ?? '') ?>">

        <label>Nueva contraseña:</label>
        <input type="password" name="nueva_password">

        <label>Confirmar nueva contraseña:</label>
        <input type="password" name="confirmar_password">

        <button type="submit">Guardar Cambios</button>
    </form>
</div>

</body>
</html>
