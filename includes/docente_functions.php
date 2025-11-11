<?php
function obtenerIdDocente($conexion, $id_usuario)
{
    try {
        $sql = "SELECT cod_docente 
                FROM docente 
                WHERE id_usuario = :id_usuario";

        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        $docente = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$docente) {
            throw new Exception("No se encontró el registro de docente para este usuario.");
        }

        return $docente['cod_docente'];
    } catch (Exception $e) {
        error_log("Error al obtener ID del docente: " . $e->getMessage());
        throw $e;
    }
}

function obtenerAsignaturasDocente($conexion, $id_docente)
{
    try {
        $sql = "SELECT ad.cod_asignacion, 
                       ad.id_docente,
                       ad.id_curso,
                       ad.id_asignatura,
                       ad.ano_lectivo,
                       c.nombre_curso,
                       g.nombre_grado,
                       a.nombre_asignatura
                FROM asignacion_docente ad
                INNER JOIN curso c ON ad.id_curso = c.cod_curso
                INNER JOIN grado g ON c.id_grado = g.cod_grado
                INNER JOIN asignatura a ON ad.id_asignatura = a.cod_asignatura
                WHERE ad.id_docente = :id_docente
                ORDER BY g.nombre_grado, c.nombre_curso, a.nombre_asignatura";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener asignaturas del docente: " . $e->getMessage());
        throw $e;
    }
}

function obtenerEstudiantesPorCurso($conexion, $id_curso, $id_asignatura)
{
    try {
        $sql = "SELECT e.cod_estudiante, 
                       CONCAT(u.nombres, ' ', u.apellidos) as nombre_completo,
                       u.numero_documento,
                       td.tipo_documento,
                       m.estado,
                       m.fecha_matricula
                FROM estudiante e
                INNER JOIN usuario u ON e.id_usuario = u.cod_usuario
                INNER JOIN tipo_documento td ON u.id_tipo_documento = td.cod_tipodocumento
                INNER JOIN matricula m ON e.cod_estudiante = m.id_estudiante
                WHERE m.id_curso = :id_curso 
                AND m.estado = 'ACTIVA'
                ORDER BY u.apellidos, u.nombres";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
        $stmt->execute();

        $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($estudiantes)) {
            return [];
        }

        return $estudiantes;
    } catch (Exception $e) {
        error_log("Error al obtener estudiantes del curso: " . $e->getMessage());
        throw new Exception("Error al obtener la lista de estudiantes");
    }
}

function obtenerEstudiantesPorDocente($conexion, $id_docente)
{
    try {
        $sql = "SELECT DISTINCT 
                       e.cod_estudiante, 
                       CONCAT(u.nombres, ' ', u.apellidos) as nombre_completo,
                       u.numero_documento,
                       td.tipo_documento,
                       c.nombre_curso,
                       c.cod_curso,
                       g.nombre_grado
                FROM estudiante e
                INNER JOIN usuario u ON e.id_usuario = u.cod_usuario
                INNER JOIN tipo_documento td ON u.id_tipo_documento = td.cod_tipodocumento
                INNER JOIN matricula m ON e.cod_estudiante = m.id_estudiante
                INNER JOIN curso c ON m.id_curso = c.cod_curso
                INNER JOIN grado g ON c.id_grado = g.cod_grado
                INNER JOIN asignacion_docente ad ON m.id_curso = ad.id_curso
                WHERE ad.id_docente = :id_docente
                AND m.estado = 'ACTIVA'
                ORDER BY g.nombre_grado, c.nombre_curso, u.apellidos, u.nombres";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
        $stmt->execute();

        $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($estudiantes)) {
            return [];
        }

        return $estudiantes;
    } catch (Exception $e) {
        error_log("Error al obtener estudiantes del docente: " . $e->getMessage());
        throw new Exception("Error al obtener la lista de estudiantes");
    }
}
