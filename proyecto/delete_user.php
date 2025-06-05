<?php
session_start();

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_var($_POST['id_usuario'], FILTER_VALIDATE_INT);
    if (!$id) exit('ID inválido');

    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuarios = ?");
    $stmt->execute([$id]);

    header('Location: admin.php');
    exit;
}
