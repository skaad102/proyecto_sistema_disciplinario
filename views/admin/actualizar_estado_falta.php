<?php
// Configuración inicial
require_once '../../config/database.php';

// La sesión ya está validada en index.php
header('Content-Type: application/json');

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
    exit();
}

// Obtener parámetros
$id_registro = $_POST['id_registro'] ?? null;
$estado = $_POST['estado'] ?? null;

// Validar parámetros
if (!$id_registro || !$estado) {
    echo json_encode(['success' => false, 'mensaje' => 'Parámetros incompletos']);
    exit();
}

// Validar que el estado sea válido
$estados_validos = ['REPORTADA', 'EN_PROCESO', 'SANCIONADA', 'ARCHIVADA'];
if (!in_array($estado, $estados_validos)) {
    echo json_encode(['success' => false, 'mensaje' => 'Estado no válido']);
    exit();
}

try {
    $database = new Database();
    $conexion = $database->connect();
    
    // Actualizar el estado
    $sql = "UPDATE registro_falta 
            SET estado = :estado 
            WHERE cod_registro = :id_registro";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
    $stmt->bindParam(':id_registro', $id_registro, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'mensaje' => 'Estado actualizado exitosamente',
            'estado' => $estado
        ]);
    } else {
        echo json_encode(['success' => false, 'mensaje' => 'Error al actualizar el estado']);
    }
    
} catch (Exception $e) {
    error_log("Error en actualizar_estado_falta.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'mensaje' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>
