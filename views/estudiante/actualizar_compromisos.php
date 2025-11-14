<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/estudiante_functions.php';

// Verificar la sesión
if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol']) !== 'estudiante') {
    http_response_code(403);
    echo json_encode(['success' => false, 'mensaje' => 'Acceso no autorizado']);
    exit();
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
    exit();
}

// Verificar parámetros
if (!isset($_POST['id_registro']) || !isset($_POST['compromisos'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Faltan parámetros requeridos']);
    exit();
}

$id_registro = intval($_POST['id_registro']);
$compromisos = trim($_POST['compromisos']);

$database = new Database();
$conexion = $database->connect();

try {
    // Obtener ID del estudiante de la sesión
    $id_usuario = $_SESSION['usuario']['id'] ?? $_SESSION['usuario']['cod_usuario'] ?? null;
    if (!$id_usuario) {
        throw new Exception("No se encontró el ID del usuario en la sesión");
    }
    
    $id_estudiante_sesion = obtenerIdEstudiante($conexion, $id_usuario);
    if (!$id_estudiante_sesion) {
        throw new Exception("No se encontró el registro de estudiante");
    }
    
    // Verificar que el registro pertenezca al estudiante
    $sql_verificar = "SELECT id_estudiante FROM registro_falta WHERE cod_registro = :id_registro";
    $stmt = $conexion->prepare($sql_verificar);
    $stmt->bindParam(':id_registro', $id_registro, PDO::PARAM_INT);
    $stmt->execute();
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$registro) {
        echo json_encode(['success' => false, 'mensaje' => 'Registro de falta no encontrado']);
        exit();
    }
    
    if ($registro['id_estudiante'] != $id_estudiante_sesion) {
        echo json_encode(['success' => false, 'mensaje' => 'No tienes permiso para editar este registro']);
        exit();
    }
    
    // Actualizar los compromisos
    $sql_actualizar = "UPDATE registro_falta 
                       SET compromisos = :compromisos 
                       WHERE cod_registro = :id_registro";
    
    $stmt = $conexion->prepare($sql_actualizar);
    $stmt->bindParam(':compromisos', $compromisos);
    $stmt->bindParam(':id_registro', $id_registro, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'mensaje' => 'Compromisos actualizados exitosamente',
            'compromisos' => $compromisos
        ]);
    } else {
        echo json_encode(['success' => false, 'mensaje' => 'Error al actualizar los compromisos']);
    }
    
} catch (Exception $e) {
    error_log("Error al actualizar compromisos: " . $e->getMessage());
    echo json_encode(['success' => false, 'mensaje' => 'Error al procesar la solicitud']);
}
?>
