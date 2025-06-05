<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

parse_str(file_get_contents("php://input"), $data);
if (empty($data['id'])) {
    echo json_encode(['error' => 'Falta id de notificación']);
    exit;
}

$user = $_SESSION['user'];
$stmtU = $pdo->prepare("SELECT id_usuarios FROM usuarios WHERE nombre_usuario = ?");
$stmtU->execute([$user]);
$id_usuario = $stmtU->fetchColumn();

if (!$id_usuario) {
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit;
}

$idNotif = (int) $data['id'];

$stmt = $pdo->prepare("UPDATE notificaciones SET leida = 1 WHERE id = ? AND id_usuario = ?");
$stmt->execute([$idNotif, $id_usuario]);

echo json_encode(['status' => 'ok']);
exit;
