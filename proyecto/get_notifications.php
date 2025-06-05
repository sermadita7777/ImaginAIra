<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$user = $_SESSION['user'];

// Obtener el ID del usuario logueado
$stmt = $pdo->prepare("SELECT id_usuarios FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$user]);
$id_usuario = $stmt->fetchColumn();

if (!$id_usuario) {
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit;
}

// Obtener las notificaciones para ese usuario
$stmt = $pdo->prepare("
    SELECT id, mensaje, fecha, leida, emisor 
    FROM notificaciones 
    WHERE id_usuario = ? 
    ORDER BY fecha DESC 
    LIMIT 20
");
$stmt->execute([$id_usuario]);
$notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si hay campo emisor, reemplazamos el nombre en el mensaje (opcional)
foreach ($notificaciones as &$n) {
    if (!empty($n['emisor'])) {
        // Si el mensaje tiene comillas (ej: "Mi publicación"), las preservamos
        $partes = explode('a tu publicación', $n['mensaje']);
        if (count($partes) === 2) {
            $n['mensaje'] = $n['emisor'] . ' ha dado like a tu publicación' . $partes[1];
        }
    }
}

echo json_encode(['notificaciones' => $notificaciones]);
