<?php
// db.php — incluye en todos los scripts que necesiten BD
$host     = 'fdb1028.awardspace.net';
$db       = '4640876_ialovers';
$user     = '4640876_ialovers';
$pass     = 'F^Gr}raB9vnaqZ_Y';
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
  exit('Error de conexión BD: ' . $e->getMessage());
}
