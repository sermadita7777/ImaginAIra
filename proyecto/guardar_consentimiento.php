
<?php
session_start();
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400); // Bad request
    exit;
}

// Extraer datos
$consent = $data['consent'] ?? null;
$visitorId = $data['visitor_id'] ?? null;

if (!$consent || !$visitorId) {
    http_response_code(400); // Bad request
    exit;
}

$consentJson = json_encode($consent);
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

// Si el usuario ha iniciado sesión, actualizar tabla usuarios
if (isset($_SESSION['user'])) {
    $stmt = $pdo->prepare("UPDATE usuarios SET cookie_consent = ? WHERE nombre_usuario = ?");
    $stmt->execute([$consentJson, $_SESSION['user']]);
}

// En cualquier caso, guardar/actualizar en tabla cookie_consents
$stmt = $pdo->prepare("INSERT INTO cookie_consents (visitor_id, consent_data, ip_address, user_agent) 
                      VALUES (?, ?, ?, ?) 
                      ON DUPLICATE KEY UPDATE 
                      consent_data = VALUES(consent_data), 
                      ip_address = VALUES(ip_address), 
                      user_agent = VALUES(user_agent)");
$stmt->execute([$visitorId, $consentJson, $ipAddress, $userAgent]);

http_response_code(204); // No content
?>
