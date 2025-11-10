<?php
session_start();
require_once '../../config/database.php';

// Verificar la sesión
if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol']) !== 'docente') {
    header('HTTP/1.1 403 Forbidden');
    exit('No autorizado');
}

// Obtener el cuerpo de la petición
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    header('HTTP/1.1 400 Bad Request');
    exit('Datos inválidos');
}

// Preparar el mensaje de error
$errorMessage = sprintf(
    "[%s] Error en %s - Curso: %s, Asignatura: %s - Mensaje: %s - Usuario: %s",
    date('Y-m-d H:i:s'),
    $data['contexto'] ?? 'Contexto no especificado',
    $data['curso'] ?? 'No especificado',
    $data['asignatura'] ?? 'No especificado',
    $data['error'] ?? 'No especificado',
    $_SESSION['usuario']['id'] ?? 'Usuario no identificado'
);

// Registrar el error
error_log($errorMessage, 3, "../../logs/frontend_errors.log");

// Responder con éxito
header('Content-Type: application/json');
echo json_encode(['success' => true]);