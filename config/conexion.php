<?php
// Configuración de credenciales del servidor MySQL
// (XAMPP por defecto: usuario "root" y contraseña vacía)
$host    = 'localhost';
$db      = 'clase_backend';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opciones de configuración del Driver PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza PDOException ante errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve arreglos asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Fuerza Prepared Statements reales
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // El detalle técnico va al log del servidor, nunca al cliente
    error_log('Error de conexión: ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["estado" => "error", "mensaje" => "Error de conexión con la base de datos."]);
    exit;
}
