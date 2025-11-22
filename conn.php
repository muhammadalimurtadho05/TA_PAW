<?php
require_once 'base.php';
global $pdo;

define("DB_HOST", "localhost");
define("DB_NAME", "ppdb");
define("DB_USER", "root");
define("DB_PASS", "");

$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  $pdo = new PDO(
    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
    DB_USER,
    DB_PASS,
    $options
  );
} catch (PDOException $e) {
  die("Koneksi gagal: " . $e->getMessage());
}
