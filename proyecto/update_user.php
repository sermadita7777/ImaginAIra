<?php
session_start();

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_var($_POST['id_usuarios'], FILTER_VALIDATE_INT);
    $nombre = trim($_POST['nombre_usuario']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $rol = trim($_POST['rol']);

    if (!$id || !$nombre || !$email || !$rol) {
        exit('Datos inválidos');
    }

    $stmt = $pdo->prepare("UPDATE usuarios SET nombre_usuario = ?, email = ?, rol = ? WHERE id_usuarios = ?");
    $stmt->execute([$nombre, $email, $rol, $id]);

    header('Location: admin.php');
    exit;
}
