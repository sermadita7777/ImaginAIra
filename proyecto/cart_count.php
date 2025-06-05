<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
  echo json_encode(['count' => 0]);
  exit;
}

$user = $_SESSION['user'];
$stmt = $pdo->prepare("SELECT id_usuarios FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$user]);
$id_usuario = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM carrito WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$count = $stmt->fetchColumn();

echo json_encode(['count' => (int)$count]);
exit;
?>
