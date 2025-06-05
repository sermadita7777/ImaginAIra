<?php
// toggle_like.php
session_start();
require 'db.php';
header('Content-Type: application/json');

// Verificar que el usuario está logueado
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

parse_str(file_get_contents("php://input"), $data);
if (empty($data['contenido_id'])) {
    echo json_encode(['error' => 'Falta contenido_id']);
    exit;
}

$contenidoId = (int) $data['contenido_id'];
if ($contenidoId <= 0) {
    echo json_encode(['error' => 'contenido_id inválido']);
    exit;
}

// Obtener id_usuario del usuario que da like
$user = $_SESSION['user'];
$stmtU = $pdo->prepare("SELECT id_usuarios FROM usuarios WHERE nombre_usuario = ?");
$stmtU->execute([$user]);
$id_usuario = $stmtU->fetchColumn();
if (!$id_usuario) {
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit;
}

// Comprobar si ya existe entrada en contenido_likes para este usuario y contenido
$stmtCheck = $pdo->prepare("
    SELECT COUNT(*) 
    FROM contenido_likes 
    WHERE usuario_id = ? AND contenido_id = ?
");
$stmtCheck->execute([$id_usuario, $contenidoId]);
$existe = (int) $stmtCheck->fetchColumn();

if ($existe > 0) {
    // Ya había “like”: lo quitamos
    $stmtDel = $pdo->prepare("
      DELETE FROM contenido_likes 
      WHERE usuario_id = ? AND contenido_id = ?
    ");
    $stmtDel->execute([$id_usuario, $contenidoId]);
    $newStatus = 'unliked';
} else {
    // No había “like”: lo insertamos
    $stmtIns = $pdo->prepare("
      INSERT INTO contenido_likes (contenido_id, usuario_id) 
      VALUES (?, ?)
    ");
    $stmtIns->execute([$contenidoId, $id_usuario]);
    $newStatus = 'liked';

    // --- NUEVA PARTE: crear notificación ---
    // Obtener autor del contenido
    $stmtAutor = $pdo->prepare("SELECT usuario_id, titulo FROM contenidos WHERE id_contenido = ?");
    $stmtAutor->execute([$contenidoId]);
    $contenidoInfo = $stmtAutor->fetch(PDO::FETCH_ASSOC);

    if ($contenidoInfo && $contenidoInfo['usuario_id'] != $id_usuario) {
        $autorId = $contenidoInfo['usuario_id'];
        $titulo = $contenidoInfo['titulo'];

        // Crear mensaje personalizado con el nombre del que dio like
        $mensaje = "$user ha dado like a tu publicación \"$titulo\".";

        // Insertar notificación con campo emisor
        $stmtNot = $pdo->prepare("
            INSERT INTO notificaciones (id_usuario, mensaje, leida, emisor) 
            VALUES (?, ?, 0, ?)
        ");
        $stmtNot->execute([$autorId, $mensaje, $user]);
    }
}

// Devolver nuevo recuento de likes
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM contenido_likes WHERE contenido_id = ?");
$stmtCount->execute([$contenidoId]);
$newCount = (int) $stmtCount->fetchColumn();

echo json_encode([
    'status' => $newStatus,
    'count'  => $newCount
]);
exit;
