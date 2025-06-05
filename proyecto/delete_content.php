<?php
session_start();


require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_var($_POST['id_contenido'], FILTER_VALIDATE_INT);
    if (!$id) exit('ID inválido');

    $stmt = $pdo->prepare("DELETE FROM contenidos WHERE id_contenido = ?");
    $stmt->execute([$id]);

    header('Location: admin.php');
    exit;
}
