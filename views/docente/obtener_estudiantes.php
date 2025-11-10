<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/docente_functions.php';

header('Content-Type: application/json');

// Verificar la sesión y el rol
if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol']) !== 'docente') {
    error_log("Acceso no autorizado en obtener_estudiantes.php - Usuario: " . (isset($_SESSION['usuario']) ? $_SESSION['usuario']['id'] : 'No hay sesión'));
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Obtener parámetros
$id_curso = isset($_GET['curso']) ? intval($_GET['curso']) : null;
$id_asignatura = isset($_GET['asignatura']) ? intval($_GET['asignatura']) : null;

if (!$id_curso || !$id_asignatura) {
    error_log("Parámetros inválidos en obtener_estudiantes.php - curso: $id_curso, asignatura: $id_asignatura");
    http_response_code(400);
    echo json_encode(['error' => 'Parámetros incompletos o inválidos']);
    exit();
}

try {
    $database = new Database();
    $conexion = $database->connect();

    // Verificar que el docente tiene acceso a esta asignatura
    $id_docente = obtenerIdDocente($conexion, $_SESSION['usuario']['id']);

    // Verificar la asignación
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM asignacion_docente 
                               WHERE id_docente = :id_docente 
                               AND id_curso = :id_curso 
                               AND id_asignatura = :id_asignatura");
    $stmt->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
    $stmt->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
    $stmt->bindParam(':id_asignatura', $id_asignatura, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->fetchColumn() == 0) {
        error_log("Intento de acceso a curso no asignado - Docente: $id_docente, Curso: $id_curso, Asignatura: $id_asignatura");
        http_response_code(403);
        echo json_encode(['error' => 'No tiene permiso para ver este curso']);
        exit();
    }

    // Obtener estudiantes
    $estudiantes = obtenerEstudiantesPorCurso($conexion, $id_curso, $id_asignatura);
    echo json_encode($estudiantes);
} catch (Exception $e) {
    error_log("Error en obtener_estudiantes.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al obtener los estudiantes',
        'detalle' => $e->getMessage()
    ]);
}
