<?php
// get_user_likes.php
session_start();
require 'db.php';
header('Content-Type: application/json');

// Si no hay usuario autenticado, devolvemos un array vacío
if (!isset($_SESSION['user'])) {
    echo json_encode([]);
    exit;
}

$user = $_SESSION['user'];
// Obtener id_usuarios de la tabla usuarios
$stmtU = $pdo->prepare("SELECT id_usuarios FROM usuarios WHERE nombre_usuario = ?");
$stmtU->execute([$user]);
$id_usuario = $stmtU->fetchColumn();
if (!$id_usuario) {
    echo json_encode([]);
    exit;
}

// Obtener todos los contenido_id que este usuario ha "likeado"
$stmt = $pdo->prepare("SELECT contenido_id FROM contenido_likes WHERE usuario_id = ?");
$stmt->execute([$id_usuario]);
$filas = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Devolver array de enteros
echo json_encode(array_map('intval', $filas));
exit;
