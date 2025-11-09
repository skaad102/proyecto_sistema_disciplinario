<?php
function verificarExisteDocumento($conexion, $id_tipo_documento, $numero_documento, $cod_estudiante = null)
{
    $query = "SELECT COUNT(*) FROM usuario u 
              LEFT JOIN estudiante e ON e.id_usuario = u.cod_usuario
              WHERE u.id_tipo_documento = :id_tipo_documento 
              AND u.numero_documento = :numero_documento";

    $params = [
        ':id_tipo_documento' => $id_tipo_documento,
        ':numero_documento' => $numero_documento
    ];

    if ($cod_estudiante) {
        $query .= " AND e.cod_estudiante != :cod_estudiante";
        $params[':cod_estudiante'] = $cod_estudiante;
    }

    $stmt = $conexion->prepare($query);
    foreach ($params as $param => $value) {
        $stmt->bindValue($param, $value);
    }
    $stmt->execute();

    return $stmt->fetchColumn() > 0;
}

function insertarUsuario($conexion, $datos)
{
    $sql = "INSERT INTO usuario (usuario, clave, id_rol, id_tipo_documento, 
            numero_documento, nombres, apellidos, telefono, correo, direccion) 
            VALUES (:usuario, :clave, :id_rol, :id_tipo_documento,
            :numero_documento, :nombres, :apellidos, :telefono, :correo, :direccion)";

    $stmt = $conexion->prepare($sql);
    foreach ($datos as $param => $value) {
        $stmt->bindValue($param, $value);
    }

    if ($stmt->execute()) {
        return $conexion->lastInsertId();
    }
    return false;
}

function insertarEstudiante($conexion, $id_usuario, $fecha_nacimiento)
{
    $sql = "INSERT INTO estudiante (id_usuario, fecha_nacimiento) VALUES (:id_usuario, :fecha_nacimiento)";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
    return $stmt->execute();
}

function actualizarUsuario($conexion, $datos, $cod_estudiante)
{
    $sql = "UPDATE usuario SET 
            usuario=:usuario,
            id_tipo_documento=:id_tipo_documento,
            numero_documento=:numero_documento,
            nombres=:nombres,
            apellidos=:apellidos,
            telefono=:telefono,
            correo=:correo,
            direccion=:direccion
            WHERE cod_usuario=(SELECT id_usuario FROM estudiante WHERE cod_estudiante=:cod_estudiante)";

    $stmt = $conexion->prepare($sql);
    foreach ($datos as $param => $value) {
        $stmt->bindValue($param, $value);
    }
    $stmt->bindParam(':cod_estudiante', $cod_estudiante);

    return $stmt->execute();
}

function actualizarEstudiante($conexion, $cod_estudiante, $fecha_nacimiento)
{
    $sql = "UPDATE estudiante SET fecha_nacimiento=:fecha_nacimiento WHERE cod_estudiante=:cod_estudiante";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
    $stmt->bindParam(':cod_estudiante', $cod_estudiante);
    return $stmt->execute();
}

function eliminarEstudiante($conexion, $cod_estudiante)
{
    // Primero obtenemos el id_usuario
    $stmt = $conexion->prepare("SELECT id_usuario FROM estudiante WHERE cod_estudiante=:cod_estudiante");
    $stmt->bindParam(':cod_estudiante', $cod_estudiante);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        return ['success' => false, 'mensaje' => 'Estudiante no encontrado'];
    }

    // Iniciamos una transacción
    $conexion->beginTransaction();

    try {
        // Primero eliminamos el registro de estudiante
        $stmt = $conexion->prepare("DELETE FROM estudiante WHERE cod_estudiante=:cod_estudiante");
        $stmt->bindParam(':cod_estudiante', $cod_estudiante);
        $stmt->execute();

        // Luego eliminamos el usuario
        $stmt = $conexion->prepare("DELETE FROM usuario WHERE cod_usuario=:id_usuario");
        $stmt->bindParam(':id_usuario', $usuario['id_usuario']);
        $stmt->execute();

        $conexion->commit();
        return ['success' => true, 'mensaje' => 'Estudiante eliminado correctamente'];
    } catch (Exception $e) {
        $conexion->rollBack();
        error_log("Error al eliminar estudiante: " . $e->getMessage());
        return ['success' => false, 'mensaje' => 'Error al eliminar el estudiante'];
    }
}

function obtenerEstudiantes($conexion)
{
    try {
        $sql = "SELECT e.cod_estudiante, u.cod_usuario as id_usuario,
                   u.id_tipo_documento, u.numero_documento,
                   u.nombres, u.apellidos,
                   u.telefono, u.correo,
                   u.direccion, u.usuario,
                   td.tipo_documento as nombre_tipo_documento,
                   e.fecha_nacimiento
            FROM estudiante e
            INNER JOIN usuario u ON e.id_usuario = u.cod_usuario
            LEFT JOIN tipo_documento td ON u.id_tipo_documento = td.cod_tipodocumento
            ORDER BY e.cod_estudiante DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener estudiantes: " . $e->getMessage());
        throw $e; // Re-throw the exception for higher-level handling
    }
}

function obtenerTiposDocumento($conexion)
{
    try {
        $sql = "SELECT cod_tipodocumento, tipo_documento 
                FROM tipo_documento 
                ORDER BY tipo_documento";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener tipos de documento: " . $e->getMessage());
        throw $e;
    }
}

function obtenerCursos($conexion)
{
    try {
        $sql = "SELECT c.cod_curso, CONCAT(g.nombre_grado, ' - ', c.nombre_curso) as nombre_curso 
                FROM curso c 
                INNER JOIN grado g ON c.id_grado = g.cod_grado 
                ORDER BY g.cod_grado, c.nombre_curso";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener cursos: " . $e->getMessage());
        throw $e;
    }
}
