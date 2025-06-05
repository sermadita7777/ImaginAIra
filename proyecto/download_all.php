<?php
session_start();
require 'db.php';

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

// Obtener contenidos del carrito con BLOB y MIME
$stmt = $pdo->prepare("
    SELECT co.titulo, co.datos_blob, co.tipo_mime
    FROM carrito c
    JOIN contenidos co ON c.id_contenido = co.id_contenido
    WHERE c.id_usuario = ?
");
$stmt->execute([$id_usuario]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($items)) {
    die("Tu carrito está vacío.");
}

// Crear archivo ZIP temporal correctamente (evita advertencia deprecada)
$zipFilename = sys_get_temp_dir() . '/carrito_' . uniqid() . '.zip';
$zip = new ZipArchive();

if ($zip->open($zipFilename, ZipArchive::CREATE) !== true) {
    die("No se pudo crear el archivo ZIP.");
}

// Crear archivos temporales con los blobs para incluir en el ZIP
$tempFiles = [];

foreach ($items as $item) {
    $extension = mime2ext($item['tipo_mime']) ?: 'bin';
    $safeTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $item['titulo']);
    $tmpFile = tempnam(sys_get_temp_dir(), 'file_') . '.' . $extension;

    file_put_contents($tmpFile, $item['datos_blob']);
    $zip->addFile($tmpFile, $safeTitle . '.' . $extension);
    $tempFiles[] = $tmpFile;
}

$zip->close();

// Limpiar salida previa
if (ob_get_length()) {
    ob_clean();
}
flush();

// Enviar ZIP al navegador
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="mi_carrito.zip"');
header('Content-Length: ' . filesize($zipFilename));
readfile($zipFilename);

// Borrar ZIP y archivos temporales
unlink($zipFilename);
foreach ($tempFiles as $file) {
    unlink($file);
}

// Vaciar el carrito del usuario después de la descarga
$stmt = $pdo->prepare("DELETE FROM carrito WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);

exit;

// Función para convertir MIME a extensión de archivo
function mime2ext($mime) {
    $map = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'application/pdf' => 'pdf',
        'text/plain' => 'txt',
        'application/zip' => 'zip',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
    ];
    return $map[$mime] ?? null;
}
