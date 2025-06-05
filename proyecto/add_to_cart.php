<?php
// add_to_cart.php
session_start();
require 'db.php'; // Asegúrate que conecta a $pdo

header('Content-Type: application/json');

// Verifica si el usuario está logueado
if (!isset($_SESSION['user'])) {
  echo json_encode(['error' => 'Usuario no autenticado']);
  exit;
}

// Obtener ID del usuario desde la base de datos
$stmt = $pdo->prepare("SELECT id_usuarios FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$_SESSION['user']]);
$userId = $stmt->fetchColumn();

if (!$userId) {
  echo json_encode(['error' => 'Usuario no encontrado']);
  exit;
}

// Obtener ID del contenido
$idContenido = $_POST['id_contenido'] ?? null;

if (!$idContenido) {
  echo json_encode(['error' => 'Falta el ID del contenido']);
  exit;
}

// Verificar si ya existe en el carrito
$stmt = $pdo->prepare("SELECT COUNT(*) FROM carrito WHERE id_usuario = ? AND id_contenido = ?");
$stmt->execute([$userId, $idContenido]);
if ($stmt->fetchColumn() > 0) {
  echo json_encode(['status' => 'ya_existe']);
  exit;
}

// Insertar en el carrito
$stmt = $pdo->prepare("INSERT INTO carrito (id_usuario, id_contenido) VALUES (?, ?)");
$stmt->execute([$userId, $idContenido]);

echo json_encode(['status' => 'ok']);
