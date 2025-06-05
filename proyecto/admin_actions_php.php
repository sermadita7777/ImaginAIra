<?php
// update_user.php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre_usuario'] ?? '';
    $email = $_POST['email'] ?? '';

    if ($id && $nombre && $email) {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre_usuario = ?, email = ? WHERE id_usuarios = ?");
        $stmt->execute([$nombre, $email, $id]);
    }
}
header('Location: admin.php');
exit;
