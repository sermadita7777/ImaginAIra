<?php
// download.php
require 'db.php';
session_start();

if (!isset($_SESSION['user'])) {
    die('Acceso denegado.');
}

$user = $_SESSION['user'];

// Obtener ID del usuario
$stmt = $pdo->prepare("SELECT id_usuarios FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$user]);
$id_usuario = $stmt->fetchColumn();

if (!$id_usuario) {
    die("Usuario no válido.");
}

// Validar el ID recibido por GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID inválido.');
}

$id = (int) $_GET['id'];

// Consultar el archivo en la base de datos
$stmt = $pdo->prepare("SELECT titulo, datos_blob, tipo_mime FROM contenidos WHERE id_contenido = ?");
$stmt->execute([$id]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    die('Archivo no encontrado.');
}

// Limpiar nombre para evitar errores en el navegador
$filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file['titulo']);
$ext = '';

switch ($file['tipo_mime']) {
    case 'image/jpeg':
    case 'image/jpg':
        $ext = '.jpg'; break;
    case 'image/png':
        $ext = '.png'; break;
    case 'application/pdf':
        $ext = '.pdf'; break;
    case 'text/plain':
        $ext = '.txt'; break;
    default:
        $ext = '';
}

// Enviar archivo al navegador
header('Content-Description: File Transfer');
header('Content-Type: ' . $file['tipo_mime']);
header('Content-Disposition: attachment; filename="' . $filename . $ext . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . strlen($file['datos_blob']));

echo $file['datos_blob'];

// Eliminar este archivo del carrito del usuario
$stmt = $pdo->prepare("DELETE FROM carrito WHERE id_usuario = ? AND id_contenido = ?");
$stmt->execute([$id_usuario, $id]);

exit;
