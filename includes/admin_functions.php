<?php

function insertarAsignatura($conexion, $datos)
{
    try {
        // Validar que el nombre no esté vacío
        if (empty(trim($datos[':nombre_asignatura']))) {
            return [
                'success' => false,
                'mensaje' => 'El nombre de la asignatura no puede estar vacío.'
            ];
        }

        $sql = "INSERT INTO asignatura (nombre_asignatura, descripcion) 
                VALUES (:nombre_asignatura, :descripcion)";

        $stmt = $conexion->prepare($sql);
        foreach ($datos as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        if ($stmt->execute()) {
            return [
                'success' => true,
                'mensaje' => 'Asignatura creada exitosamente.',
                'id' => $conexion->lastInsertId()
            ];
        }
        return [
            'success' => false,
            'mensaje' => 'Error al crear la asignatura.'
        ];
    } catch (PDOException $e) {
        // Verificar si es error de duplicado
        if ($e->getCode() == 23000) {
            return [
                'success' => false,
                'mensaje' => 'Ya existe una asignatura con ese nombre.'
            ];
        }
        error_log("Error al insertar asignatura: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

function actualizarAsignatura($conexion, $datos, $cod_asignatura)
{
    try {
        // Validar que el nombre no esté vacío
        if (empty(trim($datos[':nombre_asignatura']))) {
            return [
                'success' => false,
                'mensaje' => 'El nombre de la asignatura no puede estar vacío.'
            ];
        }

        $sql = "UPDATE asignatura SET 
                nombre_asignatura=:nombre_asignatura,
                descripcion=:descripcion
                WHERE cod_asignatura=:cod_asignatura";

        $stmt = $conexion->prepare($sql);
        foreach ($datos as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        $stmt->bindParam(':cod_asignatura', $cod_asignatura);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'mensaje' => 'Asignatura actualizada exitosamente.'
            ];
        }
        return [
            'success' => false,
            'mensaje' => 'Error al actualizar la asignatura.'
        ];
    } catch (PDOException $e) {
        // Verificar si es error de duplicado
        if ($e->getCode() == 23000) {
            return [
                'success' => false,
                'mensaje' => 'Ya existe una asignatura con ese nombre.'
            ];
        }
        error_log("Error al actualizar asignatura: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
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

// MARK: DOCENTE

function obtenerTiposDocumento($conexion)
{
    try {
        $sql = "SELECT cod_tipodocumento, tipo_documento FROM tipo_documento ORDER BY tipo_documento";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener tipos de documento: " . $e->getMessage());
        throw $e;
    }
}


function crearUsuarioYDocente($conexion, $datosUsuario, $datosDocente)
{
    try {
        // PASO 0: Verificar ANTES de iniciar la transacción si ya existe el documento
        $sqlCheck = "SELECT u.cod_usuario, u.id_rol, d.cod_docente 
                     FROM usuario u 
                     LEFT JOIN docente d ON u.cod_usuario = d.id_usuario
                     WHERE u.id_tipo_documento = :id_tipo_documento 
                     AND u.numero_documento = :numero_documento";
        $stmtCheck = $conexion->prepare($sqlCheck);
        $stmtCheck->bindValue(':id_tipo_documento', $datosUsuario[':id_tipo_documento']);
        $stmtCheck->bindValue(':numero_documento', $datosUsuario[':numero_documento']);
        $stmtCheck->execute();
        $usuarioExistente = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($usuarioExistente) {
            // Si existe el usuario Y tiene docente asociado, es un duplicado real
            if ($usuarioExistente['cod_docente'] !== null) {
                return [
                    'success' => false,
                    'mensaje' => 'Ya existe un docente registrado con este tipo y número de documento.'
                ];
            } 
            // Si el usuario existe pero es de otro rol (estudiante o directivo)
            else if ($usuarioExistente['id_rol'] != 2) {
                return [
                    'success' => false,
                    'mensaje' => 'Ya existe un usuario registrado con este documento en otro rol (Estudiante o Directivo).'
                ];
            }
            // Si existe el usuario CON rol docente pero NO tiene registro en tabla docente (huérfano)
            else if ($usuarioExistente['id_rol'] == 2 && $usuarioExistente['cod_docente'] === null) {
                // Eliminarlo ANTES de la transacción
                $sqlDeleteHuerfano = "DELETE FROM usuario WHERE cod_usuario = :cod_usuario";
                $stmtDeleteHuerfano = $conexion->prepare($sqlDeleteHuerfano);
                $stmtDeleteHuerfano->bindValue(':cod_usuario', $usuarioExistente['cod_usuario']);
                $stmtDeleteHuerfano->execute();
            }
        }

        // AHORA SÍ iniciar la transacción para crear todo
        $conexion->beginTransaction();
        // Paso 1: Crear el usuario
        $sqlUsuario = "INSERT INTO usuario (id_tipo_documento, numero_documento, nombres, apellidos, telefono, correo, direccion, usuario, clave, id_rol, estado) 
                       VALUES (:id_tipo_documento, :numero_documento, :nombres, :apellidos, :telefono, :correo, :direccion, :usuario, :clave, 2, 'ACTIVO')";

        $stmtUsuario = $conexion->prepare($sqlUsuario);
        foreach ($datosUsuario as $param => $value) {
            $stmtUsuario->bindValue($param, $value);
        }

        if (!$stmtUsuario->execute()) {
            $conexion->rollBack();
            return [
                'success' => false,
                'mensaje' => 'Error al crear el usuario.'
            ];
        }

        $id_usuario = $conexion->lastInsertId();

        // Paso 2: Crear el docente
        $sqlDocente = "INSERT INTO docente (id_usuario, especialidad) 
                       VALUES (:id_usuario, :especialidad)";

        $stmtDocente = $conexion->prepare($sqlDocente);
        $stmtDocente->bindValue(':id_usuario', $id_usuario);
        $stmtDocente->bindValue(':especialidad', $datosDocente[':especialidad']);

        if (!$stmtDocente->execute()) {
            $conexion->rollBack();
            return [
                'success' => false,
                'mensaje' => 'Error al registrar como docente.'
            ];
        }

        $conexion->commit();
        return [
            'success' => true,
            'mensaje' => 'Usuario y docente creados exitosamente.',
            'id_docente' => $conexion->lastInsertId()
        ];
    } catch (Exception $e) {
        $conexion->rollBack();
        error_log("Error al crear usuario y docente: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
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
        // Verificar si la columna estado existe en la tabla docente
        $checkColumn = "SHOW COLUMNS FROM docente LIKE 'estado'";
        $stmtCheck = $conexion->query($checkColumn);
        $hasEstado = $stmtCheck->rowCount() > 0;

        if ($hasEstado) {
            $sql = "SELECT d.cod_docente, d.id_usuario, u.nombres, u.apellidos, u.numero_documento, d.especialidad, d.estado
                    FROM docente d
                    INNER JOIN usuario u ON d.id_usuario = u.cod_usuario
                    ORDER BY u.apellidos, u.nombres";
        } else {
            // Si no existe la columna estado, usar 'ACTIVO' por defecto
            $sql = "SELECT d.cod_docente, d.id_usuario, u.nombres, u.apellidos, u.numero_documento, d.especialidad, 'ACTIVO' as estado
                    FROM docente d
                    INNER JOIN usuario u ON d.id_usuario = u.cod_usuario
                    ORDER BY u.apellidos, u.nombres";
        }

        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener todos los docentes: " . $e->getMessage());
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

function obtenerDocentePorId($conexion, $cod_docente)
{
    try {
        $sql = "SELECT d.cod_docente, d.id_usuario, d.especialidad, 
                       u.id_tipo_documento, u.numero_documento, u.nombres, u.apellidos, 
                       u.telefono, u.correo, u.direccion, u.usuario
                FROM docente d
                INNER JOIN usuario u ON d.id_usuario = u.cod_usuario
                WHERE d.cod_docente = :cod_docente";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':cod_docente', $cod_docente);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener docente por ID: " . $e->getMessage());
        throw $e;
    }
}

// FIN CRUD docente

// MARK: ESTUDIANTE
function crearUsuarioEstudiante($conexion, $datosUsuario)
{
    $sql = "INSERT INTO usuario (id_tipo_documento, numero_documento, nombres, apellidos, telefono, correo, direccion, usuario, clave, id_rol, estado) 
            VALUES (:id_tipo_documento, :numero_documento, :nombres, :apellidos, :telefono, :correo, :direccion, :usuario, :clave, 3, 'ACTIVO')";

    $stmt = $conexion->prepare($sql);
    foreach ($datosUsuario as $param => $value) {
        $stmt->bindValue($param, $value);
    }

    if ($stmt->execute()) {
        return $conexion->lastInsertId();
    }
    return false;
}

function insertarEstudiante($conexion, $datos)
{
    $sql = "INSERT INTO estudiante (id_usuario, fecha_nacimiento) 
            VALUES (:id_usuario, :fecha_nacimiento)";

    $stmt = $conexion->prepare($sql);
    foreach ($datos as $param => $value) {
        $stmt->bindValue($param, $value);
    }

    if ($stmt->execute()) {
        return $conexion->lastInsertId();
    }
    return false;
}

function crearUsuarioYEstudiante($conexion, $datosUsuario, $datosEstudiante)
{
    try {
        // PASO 0: Verificar ANTES de iniciar la transacción si ya existe el documento
        $sqlCheck = "SELECT u.cod_usuario, u.id_rol, e.cod_estudiante 
                     FROM usuario u 
                     LEFT JOIN estudiante e ON u.cod_usuario = e.id_usuario
                     WHERE u.id_tipo_documento = :id_tipo_documento 
                     AND u.numero_documento = :numero_documento";
        $stmtCheck = $conexion->prepare($sqlCheck);
        $stmtCheck->bindValue(':id_tipo_documento', $datosUsuario[':id_tipo_documento']);
        $stmtCheck->bindValue(':numero_documento', $datosUsuario[':numero_documento']);
        $stmtCheck->execute();
        $usuarioExistente = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($usuarioExistente) {
            // Si existe el usuario Y tiene estudiante asociado, es un duplicado real
            if ($usuarioExistente['cod_estudiante'] !== null) {
                return [
                    'success' => false,
                    'mensaje' => 'Ya existe un estudiante registrado con este tipo y número de documento.'
                ];
            } 
            // Si el usuario existe pero es de otro rol (docente o directivo)
            else if ($usuarioExistente['id_rol'] != 3) {
                return [
                    'success' => false,
                    'mensaje' => 'Ya existe un usuario registrado con este documento en otro rol (Docente o Directivo).'
                ];
            }
            // Si existe el usuario CON rol estudiante pero NO tiene registro en tabla estudiante (huérfano)
            else if ($usuarioExistente['id_rol'] == 3 && $usuarioExistente['cod_estudiante'] === null) {
                // Eliminarlo ANTES de la transacción
                $sqlDeleteHuerfano = "DELETE FROM usuario WHERE cod_usuario = :cod_usuario";
                $stmtDeleteHuerfano = $conexion->prepare($sqlDeleteHuerfano);
                $stmtDeleteHuerfano->bindValue(':cod_usuario', $usuarioExistente['cod_usuario']);
                $stmtDeleteHuerfano->execute();
            }
        }

        // AHORA SÍ iniciar la transacción para crear todo
        $conexion->beginTransaction();

        // Paso 1: Crear el usuario
        $sqlUsuario = "INSERT INTO usuario (id_tipo_documento, numero_documento, nombres, apellidos, telefono, correo, direccion, usuario, clave, id_rol, estado) 
                       VALUES (:id_tipo_documento, :numero_documento, :nombres, :apellidos, :telefono, :correo, :direccion, :usuario, :clave, 3, 'ACTIVO')";

        $stmtUsuario = $conexion->prepare($sqlUsuario);
        foreach ($datosUsuario as $param => $value) {
            $stmtUsuario->bindValue($param, $value);
        }

        if (!$stmtUsuario->execute()) {
            $conexion->rollBack();
            return [
                'success' => false,
                'mensaje' => 'Error al crear el usuario.'
            ];
        }

        $id_usuario = $conexion->lastInsertId();

        // Paso 2: Crear el estudiante
        $sqlEstudiante = "INSERT INTO estudiante (id_usuario, fecha_nacimiento) 
                          VALUES (:id_usuario, :fecha_nacimiento)";

        $stmtEstudiante = $conexion->prepare($sqlEstudiante);
        $stmtEstudiante->bindValue(':id_usuario', $id_usuario);
        $stmtEstudiante->bindValue(':fecha_nacimiento', $datosEstudiante[':fecha_nacimiento']);

        if (!$stmtEstudiante->execute()) {
            $conexion->rollBack();
            return [
                'success' => false,
                'mensaje' => 'Error al registrar como estudiante.'
            ];
        }

        $conexion->commit();
        return [
            'success' => true,
            'mensaje' => 'Usuario y estudiante creados exitosamente.',
            'id_estudiante' => $conexion->lastInsertId()
        ];
    } catch (Exception $e) {
        $conexion->rollBack();
        error_log("Error al crear usuario y estudiante: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

function actualizarEstudiante($conexion, $datosUsuario, $datosEstudiante, $cod_estudiante)
{
    try {
        $conexion->beginTransaction();

        // Primero obtener el id_usuario del estudiante
        $sqlGetUsuario = "SELECT id_usuario FROM estudiante WHERE cod_estudiante = :cod_estudiante";
        $stmtGetUsuario = $conexion->prepare($sqlGetUsuario);
        $stmtGetUsuario->bindParam(':cod_estudiante', $cod_estudiante);
        $stmtGetUsuario->execute();
        $resultado = $stmtGetUsuario->fetch(PDO::FETCH_ASSOC);

        if (!$resultado) {
            $conexion->rollBack();
            return false;
        }

        $id_usuario = $resultado['id_usuario'];

        // Actualizar datos del usuario
        $sqlUsuario = "UPDATE usuario SET 
                      id_tipo_documento = :id_tipo_documento,
                      numero_documento = :numero_documento,
                      nombres = :nombres,
                      apellidos = :apellidos,
                      telefono = :telefono,
                      correo = :correo,
                      direccion = :direccion
                      WHERE cod_usuario = :cod_usuario";

        $stmtUsuario = $conexion->prepare($sqlUsuario);
        foreach ($datosUsuario as $param => $value) {
            $stmtUsuario->bindValue($param, $value);
        }
        $stmtUsuario->bindParam(':cod_usuario', $id_usuario);

        if (!$stmtUsuario->execute()) {
            $conexion->rollBack();
            return false;
        }

        // Actualizar datos del estudiante
        $sqlEstudiante = "UPDATE estudiante SET 
                         fecha_nacimiento = :fecha_nacimiento
                         WHERE cod_estudiante = :cod_estudiante";

        $stmtEstudiante = $conexion->prepare($sqlEstudiante);
        foreach ($datosEstudiante as $param => $value) {
            $stmtEstudiante->bindValue($param, $value);
        }
        $stmtEstudiante->bindParam(':cod_estudiante', $cod_estudiante);

        if (!$stmtEstudiante->execute()) {
            $conexion->rollBack();
            return false;
        }

        $conexion->commit();
        return true;
    } catch (Exception $e) {
        $conexion->rollBack();
        error_log("Error al actualizar estudiante: " . $e->getMessage());
        return false;
    }
}

function obtenerTodosEstudiantes($conexion)
{
    try {
        $sql = "SELECT e.cod_estudiante, e.id_usuario, u.nombres, u.apellidos, u.numero_documento, e.fecha_nacimiento
                FROM estudiante e
                INNER JOIN usuario u ON e.id_usuario = u.cod_usuario
                ORDER BY u.apellidos, u.nombres";

        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener todos los estudiantes: " . $e->getMessage());
        throw $e;
    }
}

function obtenerEstudiantePorId($conexion, $cod_estudiante)
{
    try {
        $sql = "SELECT e.cod_estudiante, e.id_usuario, e.fecha_nacimiento, 
                       u.id_tipo_documento, u.numero_documento, u.nombres, u.apellidos, 
                       u.telefono, u.correo, u.direccion, u.usuario
                FROM estudiante e
                INNER JOIN usuario u ON e.id_usuario = u.cod_usuario
                WHERE e.cod_estudiante = :cod_estudiante";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':cod_estudiante', $cod_estudiante);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener estudiante por ID: " . $e->getMessage());
        throw $e;
    }
}

function desactivarEstudiante($conexion, $cod_estudiante)
{
    try {
        // Obtener el id_usuario del estudiante
        $sqlGetUsuario = "SELECT id_usuario FROM estudiante WHERE cod_estudiante = :cod_estudiante";
        $stmtGetUsuario = $conexion->prepare($sqlGetUsuario);
        $stmtGetUsuario->bindParam(':cod_estudiante', $cod_estudiante);
        $stmtGetUsuario->execute();
        $resultado = $stmtGetUsuario->fetch(PDO::FETCH_ASSOC);

        if (!$resultado) {
            return [
                'success' => false,
                'mensaje' => 'Estudiante no encontrado.'
            ];
        }

        // Desactivar el usuario asociado
        $sql = "UPDATE usuario SET estado = 'INACTIVO' WHERE cod_usuario = :cod_usuario";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':cod_usuario', $resultado['id_usuario']);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'mensaje' => 'Estudiante desactivado correctamente.'
            ];
        } else {
            return [
                'success' => false,
                'mensaje' => 'Error al desactivar el estudiante.'
            ];
        }
    } catch (Exception $e) {
        error_log("Error al desactivar estudiante: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

function activarEstudiante($conexion, $cod_estudiante)
{
    try {
        // Obtener el id_usuario del estudiante
        $sqlGetUsuario = "SELECT id_usuario FROM estudiante WHERE cod_estudiante = :cod_estudiante";
        $stmtGetUsuario = $conexion->prepare($sqlGetUsuario);
        $stmtGetUsuario->bindParam(':cod_estudiante', $cod_estudiante);
        $stmtGetUsuario->execute();
        $resultado = $stmtGetUsuario->fetch(PDO::FETCH_ASSOC);

        if (!$resultado) {
            return [
                'success' => false,
                'mensaje' => 'Estudiante no encontrado.'
            ];
        }

        // Activar el usuario asociado
        $sql = "UPDATE usuario SET estado = 'ACTIVO' WHERE cod_usuario = :cod_usuario";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':cod_usuario', $resultado['id_usuario']);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'mensaje' => 'Estudiante activado correctamente.'
            ];
        } else {
            return [
                'success' => false,
                'mensaje' => 'Error al activar el estudiante.'
            ];
        }
    } catch (Exception $e) {
        error_log("Error al activar estudiante: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}
