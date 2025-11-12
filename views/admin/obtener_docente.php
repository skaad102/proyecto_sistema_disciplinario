<?php
// Limpiar cualquier salida previa
ob_start();

// Configuración inicial
require_once '../../config/database.php';
require_once '../../includes/admin_functions.php';

// Limpiar el buffer de salida
ob_end_clean();

// Establecer header JSON antes de cualquier salida
header('Content-Type: application/json; charset=utf-8');

// Verificar que sea una petición AJAX
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de docente no válido'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $database = new Database();
    $conexion = $database->connect();
    
    $cod_docente = intval($_GET['id']);
    $docente = obtenerDocentePorId($conexion, $cod_docente);
    
    if ($docente) {
        echo json_encode($docente, JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Docente no encontrado'], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener datos del docente: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
exit;
