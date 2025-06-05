<?php
session_start();

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_var($_POST['id_contenido'], FILTER_VALIDATE_INT);
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);

    if (!$id || !$titulo || !$descripcion) {
        exit('Datos inválidos');
    }

    $stmt = $pdo->prepare("UPDATE contenidos SET titulo = ?, descripcion = ? WHERE id_contenido = ?");
    $stmt->execute([$titulo, $descripcion, $id]);

    header('Location: admin.php');
    exit;
}
