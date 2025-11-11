<?php

function insertarAsignatura($conexion, $datos)
{
    $sql = "INSERT INTO asignatura (nombre_asignatura, descripcion) 
            VALUES (:nombre_asignatura, :descripcion)";

    $stmt = $conexion->prepare($sql);
    foreach ($datos as $param => $value) {
        $stmt->bindValue($param, $value);
    }

    if ($stmt->execute()) {
        return $conexion->lastInsertId();
    }
    return false;
}

function actualizarAsignatura($conexion, $datos, $cod_asignatura)
{
    $sql = "UPDATE asignatura SET 
            nombre_asignatura=:nombre_asignatura,
            descripcion=:descripcion
            WHERE cod_asignatura=:cod_asignatura";

    $stmt = $conexion->prepare($sql);
    foreach ($datos as $param => $value) {
        $stmt->bindValue($param, $value);
    }
    $stmt->bindParam(':cod_asignatura', $cod_asignatura);

    return $stmt->execute();
}

function eliminarAsignatura($conexion, $cod_asignatura)
{
    try {
        // Verificar si hay asignaciones activas con esta asignatura
        $sqlVerificar = "SELECT COUNT(*) as total 
                        FROM asignacion_docente 
                        WHERE id_asignatura = :cod_asignatura";

        $stmtVerificar = $conexion->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':cod_asignatura', $cod_asignatura);
        $stmtVerificar->execute();
        $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

        // Si hay asignaciones, no permitir eliminar
        if ($resultado['total'] > 0) {
            return [
                'success' => false,
                'mensaje' => 'No se puede eliminar la asignatura porque tiene cursos asignados actualmente. Elimine primero las asignaciones de docentes.'
            ];
        }

        // Si no hay asignaciones, proceder con la eliminación
        $sql = "DELETE FROM asignatura WHERE cod_asignatura=:cod_asignatura";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':cod_asignatura', $cod_asignatura);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'mensaje' => 'Asignatura eliminada correctamente.'
            ];
        } else {
            return [
                'success' => false,
                'mensaje' => 'Error al eliminar la asignatura.'
            ];
        }
    } catch (Exception $e) {
        error_log("Error al eliminar asignatura: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

function obtenerTodasAsignaturas($conexion)
{
    try {
        $sql = "SELECT cod_asignatura, nombre_asignatura, descripcion
                FROM asignatura
                ORDER BY nombre_asignatura";

        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener todas las asignaturas: " . $e->getMessage());
        throw $e;
    }
}

// CRUD docente
function insertarDocente($conexion, $datos)
{
    $sql = "INSERT INTO docente (id_usuario, especialidad) 
            VALUES (:id_usuario, :especialidad)";

    $stmt = $conexion->prepare($sql);
    foreach ($datos as $param => $value) {
        $stmt->bindValue($param, $value);
    }

    if ($stmt->execute()) {
        return $conexion->lastInsertId();
    }
    return false;
}
function actualizarDocente($conexion, $datos, $cod_docente)
{
    $sql = "UPDATE docente SET 
            especialidad=:especialidad
            WHERE cod_docente=:cod_docente";

    $stmt = $conexion->prepare($sql);
    foreach ($datos as $param => $value) {
        $stmt->bindValue($param, $value);
    }
    $stmt->bindParam(':cod_docente', $cod_docente);

    return $stmt->execute();
}

function desactivarDocente($conexion, $cod_docente)
{
    try {
        // Marcar como INACTIVO en lugar de eliminar
        $sql = "UPDATE docente SET estado = 'INACTIVO' WHERE cod_docente = :cod_docente";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':cod_docente', $cod_docente);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'mensaje' => 'Docente desactivado correctamente.'
            ];
        } else {
            return [
                'success' => false,
                'mensaje' => 'Error al desactivar el docente.'
            ];
        }
    } catch (Exception $e) {
        error_log("Error al desactivar docente: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

function obtenerTodosDocentes($conexion)
{
    try {
        $sql = "SELECT d.cod_docente, d.id_usuario, u.nombres, u.apellidos, u.numero_documento, d.especialidad, d.estado
                FROM docente d
                INNER JOIN usuario u ON d.id_usuario = u.cod_usuario
                ORDER BY u.apellidos, u.nombres";

        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener todos los docentes: " . $e->getMessage());
        throw $e;
    }
}

function obtenerUsuariosDisponiblesParaDocente($conexion)
{
    try {
        // Obtener usuarios con rol 'docente' que NO están ya en la tabla docente
        $sql = "SELECT u.cod_usuario, u.nombres, u.apellidos, u.numero_documento
                FROM usuario u
                INNER JOIN rol r ON u.id_rol = r.cod_rol
                WHERE LOWER(r.nombre_rol) = 'docente'
                AND u.cod_usuario NOT IN (SELECT id_usuario FROM docente)
                ORDER BY u.apellidos, u.nombres";

        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener usuarios disponibles: " . $e->getMessage());
        throw $e;
    }
}

function activarDocente($conexion, $cod_docente)
{
    try {
        $sql = "UPDATE docente SET estado = 'ACTIVO' WHERE cod_docente = :cod_docente";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':cod_docente', $cod_docente);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'mensaje' => 'Docente activado correctamente.'
            ];
        } else {
            return [
                'success' => false,
                'mensaje' => 'Error al activar el docente.'
            ];
        }
    } catch (Exception $e) {
        error_log("Error al activar docente: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}
