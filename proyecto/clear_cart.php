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

$stmt = $pdo->prepare("DELETE FROM carrito WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);

header('Location: cart.php');
exit;
