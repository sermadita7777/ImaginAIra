<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

// Inicializar respuesta
$response = ['consent' => null];

// Verificar si hay usuario con sesión
if (isset($_SESSION['user'])) {
    $stmt = $pdo->prepare("SELECT cookie_consent FROM usuarios WHERE nombre_usuario = ?");
    $stmt->execute([$_SESSION['user']]);
    $result = $stmt->fetch();
    
    if ($result && $result['cookie_consent']) {
        $response['consent'] = json_decode($result['cookie_consent'], true);
        echo json_encode($response);
        exit;
    }
}

// Si no hay usuario con sesión o no tiene consentimiento guardado,
// verificar por visitor_id
if (isset($_GET['visitor_id'])) {
    $visitorId = $_GET['visitor_id'];
    $stmt = $pdo->prepare("SELECT consent_data FROM cookie_consents WHERE visitor_id = ?");
    $stmt->execute([$visitorId]);
    $result = $stmt->fetch();
    
    if ($result) {
        $response['consent'] = json_decode($result['consent_data'], true);
    }
}

echo json_encode($response);
?>