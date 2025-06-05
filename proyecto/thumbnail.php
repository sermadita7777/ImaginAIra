<?php
// thumbnail.php
require 'db.php';

if (!isset($_GET['id_contenido'])) {
  http_response_code(400);
  exit;
}

$id = (int) $_GET['id_contenido'];
$stmt = $pdo->prepare("
  SELECT datos_blob, tipo_mime, miniatura_blob, miniatura_mime
    FROM contenidos
   WHERE id_contenido = ?
   LIMIT 1
");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
  http_response_code(404);
  exit;
}

// Si la URL incluye "&thumb=1" y existe miniatura, devolvemos la miniatura
if (isset($_GET['thumb']) && $_GET['thumb'] === '1' && !empty($row['miniatura_blob'])) {
  header('Content-Type: ' . $row['miniatura_mime']);
  echo $row['miniatura_blob'];
  exit;
}

// En caso contrario, enviamos el blob completo (imagen o PDF binario);
header('Content-Type: ' . $row['tipo_mime']);
echo $row['datos_blob'];
