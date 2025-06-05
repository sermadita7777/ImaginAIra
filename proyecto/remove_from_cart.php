<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}

$user = $_SESSION['user'];
$stmt = $pdo->prepare("SELECT id_usuarios FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$user]);
$id_usuario = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_contenido'])) {
  $id = (int) $_POST['id_contenido'];
  $stmt = $pdo->prepare("DELETE FROM carrito WHERE id_usuario = ? AND id_contenido = ?");
  $stmt->execute([$id_usuario, $id]);
}

header('Location: cart.php');
exit;
