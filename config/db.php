<?php
$host = $_ENV['DB_HOST'] ?? '10.1.1.7';
$db = $_ENV['DB_NAME'] ?? 'gestao_frota';
$user = $_ENV['DB_USER'] ?? 'DEV';
$pass = $_ENV['DB_PASSWORD'] ?? '#Dev@1308&COOPERANTE';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
// resto do código...
$options = [
     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
     PDO::ATTR_EMULATE_PREPARES => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int) $e->getCode());
}
?>