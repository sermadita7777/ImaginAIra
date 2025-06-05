<?php
// get_likes_count.php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_GET['contenido_id'])) {
    echo json_encode(['error' => 'Falta contenido_id']);
    exit;
}

$id_contenido = (int) $_GET['contenido_id'];
if ($id_contenido <= 0) {
    echo json_encode(['error' => 'contenido_id inválido']);
    exit;
}

// Contar cuántas filas en contenido_likes hay para ese contenido
$stmt = $pdo->prepare("SELECT COUNT(*) FROM contenido_likes WHERE contenido_id = ?");
$stmt->execute([$id_contenido]);
$count = (int) $stmt->fetchColumn();

echo json_encode(['count' => $count]);
exit;
