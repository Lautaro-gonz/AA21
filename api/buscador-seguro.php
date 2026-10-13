<?php
// ✅ CÓDIGO SEGURO: PDO + consultas preparadas con parámetros nombrados
require_once '../config/conexion.php';

header('Content-Type: application/json; charset=utf-8');

// Solo se acepta GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["estado" => "error", "mensaje" => "Método no permitido."]);
    exit;
}

// Validación de entrada: debe ser string y de largo razonable
$q = $_GET['q'] ?? '';
if (!is_string($q)) {
    http_response_code(400);
    echo json_encode(["estado" => "error", "mensaje" => "Parámetro inválido."]);
    exit;
}
$q = trim($q);
if (mb_strlen($q) > 100) {
    http_response_code(400);
    echo json_encode(["estado" => "error", "mensaje" => "La búsqueda es demasiado larga."]);
    exit;
}

try {
    // Se escapan los comodines de LIKE (% _ \) para que el usuario los busque literalmente
    $q_escapado = addcslashes($q, '%_\\');

    // La consulta lleva marcadores (:q); los datos NUNCA se concatenan al SQL
    $sql = "SELECT id, nombre, categoria, precio, stock
            FROM productos
            WHERE nombre LIKE :q_nombre OR categoria LIKE :q_categoria
            ORDER BY nombre ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':q_nombre'    => "%{$q_escapado}%",
        ':q_categoria' => "%{$q_escapado}%",
    ]);
    $resultado = $stmt->fetchAll();

    http_response_code(200);
    echo json_encode([
        "estado" => "exito",
        "total"  => count($resultado),
        "data"   => $resultado
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log('Error en buscador: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(["estado" => "error", "mensaje" => "Error interno del servidor."]);
}
