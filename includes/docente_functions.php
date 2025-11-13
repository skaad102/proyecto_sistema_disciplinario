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

// MARK: FALTAS

function obtenerTiposFalta($conexion)
{
    try {
        $sql = "SELECT cod_tipofalta, nombre_tipo, descripcion_tipo, gravedad 
                FROM tipo_falta 
                ORDER BY gravedad, nombre_tipo";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener tipos de falta: " . $e->getMessage());
        throw $e;
    }
}


function crearFalta($conexion, $datosFalta)
{
    try {
        // Validar que los campos requeridos no estén vacíos
        if (empty($datosFalta[':id_tipofalta']) || empty(trim($datosFalta[':descripcion']))) {
            return [
                'success' => false,
                'mensaje' => 'El tipo de falta y la descripción son obligatorios.'
            ];
        }

        // Crear la falta
        $sql = "INSERT INTO falta (id_tipofalta, descripcion, sancion_sugerida) 
                VALUES (:id_tipofalta, :descripcion, :sancion_sugerida)";

        $stmt = $conexion->prepare($sql);
        foreach ($datosFalta as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        if ($stmt->execute()) {
            return [
                'success' => true,
                'mensaje' => 'Falta creada exitosamente.',
                'id_falta' => $conexion->lastInsertId()
            ];
        }
        return [
            'success' => false,
            'mensaje' => 'Error al crear la falta.'
        ];
    } catch (PDOException $e) {
        // Verificar si es error de clave foránea
        if ($e->getCode() == 23000) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return [
                    'success' => false,
                    'mensaje' => 'Ya existe una falta con esa descripción.'
                ];
            }
            return [
                'success' => false,
                'mensaje' => 'Error: El tipo de falta seleccionado no existe.'
            ];
        }
        error_log("Error al crear falta: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}



function registrarFaltaEstudiante($conexion, $datosRegistro)
{
    try {
        // Validar que los campos requeridos no estén vacíos
        if (
            empty($datosRegistro[':id_estudiante']) || empty($datosRegistro[':id_docente']) ||
            empty($datosRegistro[':id_curso']) || empty($datosRegistro[':id_tipofalta']) ||
            empty(trim($datosRegistro[':descripcion_falta']))
        ) {
            return [
                'success' => false,
                'mensaje' => 'Faltan datos obligatorios para registrar la falta.'
            ];
        }

        // Iniciar transacción
        $conexion->beginTransaction();

        // 1. Primero crear la falta en la tabla falta
        $datosFalta = [
            ':id_tipofalta' => $datosRegistro[':id_tipofalta'],
            ':descripcion' => $datosRegistro[':descripcion_falta'],
            ':sancion_sugerida' => $datosRegistro[':correctivos_disciplinarios'] ?? ''
        ];

        $resultadoFalta = crearFalta($conexion, $datosFalta);

        if (!$resultadoFalta['success']) {
            $conexion->rollBack();
            return $resultadoFalta;
        }

        $id_falta = $resultadoFalta['id_falta'];

        // 2. Crear el registro de falta asociando el id_falta recién creado
        $sql = "INSERT INTO registro_falta
                (fecha_registro, hora_registro, id_estudiante, id_docente, id_curso, id_falta, 
                 descripcion_falta, descargos_estudiante, correctivos_disciplinarios, compromisos, 
                 observaciones, estado) 
                VALUES 
                (:fecha_registro, :hora_registro, :id_estudiante, :id_docente, :id_curso, :id_falta, 
                 :descripcion_falta, :descargos_estudiante, :correctivos_disciplinarios, :compromisos, 
                 :observaciones, :estado)";

        $stmt = $conexion->prepare($sql);
        
        // Usar el id_falta recién creado
        $stmt->bindValue(':fecha_registro', $datosRegistro[':fecha_registro']);
        $stmt->bindValue(':hora_registro', $datosRegistro[':hora_registro']);
        $stmt->bindValue(':id_estudiante', $datosRegistro[':id_estudiante']);
        $stmt->bindValue(':id_docente', $datosRegistro[':id_docente']);
        $stmt->bindValue(':id_curso', $datosRegistro[':id_curso']);
        $stmt->bindValue(':id_falta', $id_falta, PDO::PARAM_INT);
        $stmt->bindValue(':descripcion_falta', $datosRegistro[':descripcion_falta']);
        $stmt->bindValue(':descargos_estudiante', $datosRegistro[':descargos_estudiante'] ?? '');
        $stmt->bindValue(':correctivos_disciplinarios', $datosRegistro[':correctivos_disciplinarios'] ?? '');
        $stmt->bindValue(':compromisos', $datosRegistro[':compromisos'] ?? '');
        $stmt->bindValue(':observaciones', $datosRegistro[':observaciones'] ?? '');
        $stmt->bindValue(':estado', $datosRegistro[':estado']);

        if ($stmt->execute()) {
            $conexion->commit();
            return [
                'success' => true,
                'mensaje' => 'Falta registrada exitosamente.',
                'id_registro' => $conexion->lastInsertId(),
                'id_falta' => $id_falta
            ];
        }
        
        $conexion->rollBack();
        return [
            'success' => false,
            'mensaje' => 'Error al registrar la falta en el historial.'
        ];
    } catch (PDOException $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }
        
        // Verificar si es error de clave foránea
        if ($e->getCode() == 23000) {
            return [
                'success' => false,
                'mensaje' => 'Error: El estudiante, docente o curso seleccionado no existe.'
            ];
        }
        error_log("Error al registrar falta del estudiante: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

function obtenerTodasFaltasDocente($conexion, $id_docente)
{
    try {
        $sql = "SELECT rf.cod_registro, 
                       rf.fecha_registro,
                       rf.hora_registro,
                       e.cod_estudiante,
                       CONCAT(u.nombres, ' ', u.apellidos) as nombre_estudiante,
                       tf.nombre_tipo,
                       f.descripcion as descripcion_falta,
                       rf.estado
                FROM registro_falta rf
                INNER JOIN estudiante e ON rf.id_estudiante = e.cod_estudiante
                INNER JOIN usuario u ON e.id_usuario = u.cod_usuario
                INNER JOIN falta f ON rf.id_falta = f.cod_falta
                INNER JOIN tipo_falta tf ON f.id_tipofalta = tf.cod_tipofalta
                WHERE rf.id_docente = :id_docente
                ORDER BY rf.fecha_registro DESC, rf.hora_registro DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
        $stmt->execute();

        $faltas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($faltas)) {
            return [];
        }

        return $faltas;
    } catch (Exception $e) {
        error_log("Error al obtener faltas del docente: " . $e->getMessage());
        throw new Exception("Error al obtener la lista de faltas");
    }
}
