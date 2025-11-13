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
        $sql = "SELECT e.cod_estudiante, e.id_usuario, u.nombres, u.apellidos, u.numero_documento, e.fecha_nacimiento, u.estado
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


// MARK: GRADO
function obtenerTodosGrados($conexion)
{
    try {
        $sql = "SELECT cod_grado, nombre_grado, nivel
                FROM grado
                ORDER BY nombre_grado";

        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener todos los grados: " . $e->getMessage());
        throw $e;
    }
}
function obtenerTodosCursos($conexion)
{
    try {
        $sql = "SELECT c.cod_curso, g.nombre_grado, d.cod_docente, u.nombres, u.apellidos, c.ano_lectivo, c.estado, c.nombre_curso
                FROM curso c
                INNER JOIN grado g ON c.id_grado = g.cod_grado
                INNER JOIN docente d ON c.id_director_grupo = d.cod_docente
                INNER JOIN usuario u ON d.id_usuario = u.cod_usuario
                ORDER BY g.nombre_grado, c.ano_lectivo";

        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener todos los cursos: " . $e->getMessage());
        throw $e;
    }
}

function crearCurso($conexion, $datos)
{
    try {
        $sql = "INSERT INTO curso (id_grado, id_director_grupo, ano_lectivo, estado) 
                VALUES (:id_grado, :id_director_grupo, :ano_lectivo, :estado)";

        $stmt = $conexion->prepare($sql);
        foreach ($datos as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        if ($stmt->execute()) {
            return [
                'success' => true,
                'mensaje' => 'Curso creado exitosamente.',
                'id_curso' => $conexion->lastInsertId()
            ];
        }
        return [
            'success' => false,
            'mensaje' => 'Error al crear el curso.'
        ];
    } catch (Exception $e) {
        error_log("Error al crear curso: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

function actualizarCurso($conexion, $datos, $cod_curso)
{
    try {
        $sql = "UPDATE curso SET 
                id_grado = :id_grado,
                id_director_grupo = :id_director_grupo,
                ano_lectivo = :ano_lectivo,
                estado = :estado
                WHERE cod_curso = :cod_curso";

        $stmt = $conexion->prepare($sql);
        foreach ($datos as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        $stmt->bindParam(':cod_curso', $cod_curso);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'mensaje' => 'Curso actualizado exitosamente.'
            ];
        }
        return [
            'success' => false,
            'mensaje' => 'Error al actualizar el curso.'
        ];
    } catch (Exception $e) {
        error_log("Error al actualizar curso: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

function desactivarCurso($conexion, $cod_curso)
{
    try {
        $sql = "UPDATE curso SET estado = 'INACTIVO' WHERE cod_curso = :cod_curso";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':cod_curso', $cod_curso);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'mensaje' => 'Curso desactivado correctamente.'
            ];
        }
        return [
            'success' => false,
            'mensaje' => 'Error al desactivar el curso.'
        ];
    } catch (Exception $e) {
        error_log("Error al desactivar curso: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

function activarCurso($conexion, $cod_curso)
{
    try {
        $sql = "UPDATE curso SET estado = 'ACTIVO' WHERE cod_curso = :cod_curso";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':cod_curso', $cod_curso);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'mensaje' => 'Curso activado correctamente.'
            ];
        }
        return [
            'success' => false,
            'mensaje' => 'Error al activar el curso.'
        ];
    } catch (Exception $e) {
        error_log("Error al activar curso: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

function obtenerCursoPorId($conexion, $cod_curso)
{
    try {
        $sql = "SELECT c.cod_curso, c.id_grado, c.id_director_grupo, c.ano_lectivo, c.estado,
                       g.nombre_grado, u.nombres, u.apellidos
                FROM curso c
                INNER JOIN grado g ON c.id_grado = g.cod_grado
                INNER JOIN docente d ON c.id_director_grupo = d.cod_docente
                INNER JOIN usuario u ON d.id_usuario = u.cod_usuario
                WHERE c.cod_curso = :cod_curso";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':cod_curso', $cod_curso);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener curso por ID: " . $e->getMessage());
        throw $e;
    }
}

// MARK: ASIGNATURA
// `cod_asignacion``id_docente``id_curso``id_asignatura``ano_lectivo`

function onternerTodasAsignaciones($conexion)
{
    try {
        $sql = "SELECT ad.cod_asignacion, d.cod_docente, u.nombres, u.apellidos, 
                       c.cod_curso, g.nombre_grado, ad.ano_lectivo, a.nombre_asignatura
                FROM asignacion_docente ad
                INNER JOIN docente d ON ad.id_docente = d.cod_docente
                INNER JOIN usuario u ON d.id_usuario = u.cod_usuario
                INNER JOIN curso c ON ad.id_curso = c.cod_curso
                INNER JOIN grado g ON c.id_grado = g.cod_grado
                INNER JOIN asignatura a ON ad.id_asignatura = a.cod_asignatura
                ORDER BY u.apellidos, u.nombres, g.nombre_grado, a.nombre_asignatura";

        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener todas las asignaciones: " . $e->getMessage());
        throw $e;
    }
}

function asignarAsignaturaADocente($conexion, $datos)
{
    try {
        $sql = "INSERT INTO asignacion_docente (id_docente, id_curso, id_asignatura, ano_lectivo) 
                VALUES (:id_docente, :id_curso, :id_asignatura, :ano_lectivo)";

        $stmt = $conexion->prepare($sql);
        foreach ($datos as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        if ($stmt->execute()) {
            return [
                'success' => true,
                'mensaje' => 'Asignatura asignada al docente exitosamente.',
                'id_asignacion' => $conexion->lastInsertId()
            ];
        }
        return [
            'success' => false,
            'mensaje' => 'Error al asignar la asignatura al docente.'
        ];
    } catch (Exception $e) {
        error_log("Error al asignar asignatura a docente: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

function eliminarAsignacion($conexion, $cod_asignacion)
{
    try {
        $sql = "DELETE FROM asignacion_docente WHERE cod_asignacion = :cod_asignacion";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':cod_asignacion', $cod_asignacion);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'mensaje' => 'Asignación eliminada correctamente.'
            ];
        }
        return [
            'success' => false,
            'mensaje' => 'Error al eliminar la asignación.'
        ];
    } catch (Exception $e) {
        error_log("Error al eliminar asignación: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

// MARK MATRICULAS


// `cod_matricula``cod_matricula``id_curso``fecha_matricula``estado`
function obtenerTodasMatriculas($conexion)
{
    try {
        $sql = "SELECT m.cod_matricula, e.cod_estudiante, u.nombres, u.apellidos, 
                       c.cod_curso, g.nombre_grado, m.fecha_matricula, m.estado
                FROM matricula m
                INNER JOIN estudiante e ON m.id_estudiante = e.cod_estudiante
                INNER JOIN usuario u ON e.id_usuario = u.cod_usuario
                INNER JOIN curso c ON m.id_curso = c.cod_curso
                INNER JOIN grado g ON c.id_grado = g.cod_grado
                ORDER BY u.apellidos, u.nombres, g.nombre_grado, m.fecha_matricula";

        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener todas las matrículas: " . $e->getMessage());
        throw $e;
    }
}

function obtenerEstudiantesPorCurso($conexion, $cod_curso)
{
    try {
        $sql = "SELECT e.cod_estudiante, 
                       u.nombres, 
                       u.apellidos, 
                       u.numero_documento,
                       td.tipo_documento,
                       u.correo,
                       u.telefono,
                       m.fecha_matricula, 
                       m.estado,
                       u.estado as estado_usuario
                FROM matricula m
                INNER JOIN estudiante e ON m.id_estudiante = e.cod_estudiante
                INNER JOIN usuario u ON e.id_usuario = u.cod_usuario
                LEFT JOIN tipo_documento td ON u.id_tipo_documento = td.cod_tipodocumento
                WHERE m.id_curso = :cod_curso
                ORDER BY u.apellidos, u.nombres";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':cod_curso', $cod_curso);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener estudiantes por curso: " . $e->getMessage());
        throw $e;
    }
}

function crearMatriculaMultiple($conexion, $estudiantes_ids, $curso_id, $fecha_matricula, $estado = 'ACTIVA')
{
    $exitosas = [];
    $duplicadas = [];
    $errores = [];
    $total_procesados = 0;

    try {
        // Obtener nombres de estudiantes para los mensajes
        $placeholders = str_repeat('?,', count($estudiantes_ids) - 1) . '?';
        $sqlNombres = "SELECT e.cod_estudiante, CONCAT(u.nombres, ' ', u.apellidos) as nombre_completo
                       FROM estudiante e
                       INNER JOIN usuario u ON e.id_usuario = u.cod_usuario
                       WHERE e.cod_estudiante IN ($placeholders)";
        
        $stmtNombres = $conexion->prepare($sqlNombres);
        $stmtNombres->execute($estudiantes_ids);
        $nombresEstudiantes = $stmtNombres->fetchAll(PDO::FETCH_KEY_PAIR);

        foreach ($estudiantes_ids as $estudiante_id) {
            $total_procesados++;
            $nombreEstudiante = $nombresEstudiantes[$estudiante_id] ?? "ID: $estudiante_id";

            try {
                // Verificar si el estudiante ya está matriculado en el curso
                $sqlVerificar = "SELECT COUNT(*) as total FROM matricula 
                                WHERE id_estudiante = :id_estudiante 
                                AND id_curso = :id_curso 
                                AND estado = 'ACTIVA'";

                $stmtVerificar = $conexion->prepare($sqlVerificar);
                $stmtVerificar->bindValue(':id_estudiante', $estudiante_id);
                $stmtVerificar->bindValue(':id_curso', $curso_id);
                $stmtVerificar->execute();
                $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

                if ($resultado['total'] > 0) {
                    $duplicadas[] = $nombreEstudiante;
                    continue; // Continuar con el siguiente estudiante
                }

                // Crear la matrícula
                $sql = "INSERT INTO matricula (id_estudiante, id_curso, fecha_matricula, estado) 
                        VALUES (:id_estudiante, :id_curso, :fecha_matricula, :estado)";

                $stmt = $conexion->prepare($sql);
                $stmt->bindValue(':id_estudiante', $estudiante_id);
                $stmt->bindValue(':id_curso', $curso_id);
                $stmt->bindValue(':fecha_matricula', $fecha_matricula);
                $stmt->bindValue(':estado', $estado);

                if ($stmt->execute()) {
                    $exitosas[] = $nombreEstudiante;
                } else {
                    $errores[] = $nombreEstudiante;
                }
            } catch (PDOException $e) {
                // Detectar si es error de duplicado (clave única)
                if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $duplicadas[] = $nombreEstudiante;
                } else {
                    // Otros errores de base de datos
                    $errores[] = $nombreEstudiante . " (Error de BD)";
                    error_log("Error al matricular estudiante $nombreEstudiante: " . $e->getMessage());
                }
                continue; // Continuar con el siguiente estudiante
            }
        }

        // Construir mensaje detallado y amigable
        $mensaje = '<div style="text-align: left;">';
        
        if (count($exitosas) > 0) {
            $mensaje .= '<div style="margin-bottom: 10px;">';
            $mensaje .= '<strong style="color: #198754;">✅ Matriculados exitosamente (' . count($exitosas) . '):</strong><br>';
            $mensaje .= '<span style="margin-left: 20px;">' . implode(', ', $exitosas) . '</span>';
            $mensaje .= '</div>';
        }
        
        if (count($duplicadas) > 0) {
            $mensaje .= '<div style="margin-bottom: 10px;">';
            $mensaje .= '<strong style="color: #fd7e14;">⚠️ Ya matriculados en este curso (' . count($duplicadas) . '):</strong><br>';
            $mensaje .= '<span style="margin-left: 20px;">' . implode(', ', $duplicadas) . '</span>';
            $mensaje .= '</div>';
        }
        
        if (count($errores) > 0) {
            $mensaje .= '<div style="margin-bottom: 10px;">';
            $mensaje .= '<strong style="color: #dc3545;">❌ No se pudieron matricular (' . count($errores) . '):</strong><br>';
            $mensaje .= '<span style="margin-left: 20px;">' . implode(', ', $errores) . '</span>';
            $mensaje .= '</div>';
        }

        $mensaje .= '</div>';

        // Resumen final (título principal)
        if (count($exitosas) > 0 && count($duplicadas) == 0 && count($errores) == 0) {
            $mensaje = '<div><strong>✅ ¡Todos los estudiantes fueron matriculados exitosamente!</strong></div><hr style="margin: 10px 0;">' . $mensaje;
        } elseif (count($exitosas) == 0 && count($duplicadas) > 0 && count($errores) == 0) {
            $mensaje = '<div><strong>⚠️ Todos los estudiantes ya estaban matriculados en este curso.</strong></div><hr style="margin: 10px 0;">' . $mensaje;
        } elseif (count($exitosas) == 0 && count($errores) > 0) {
            $mensaje = '<div><strong>❌ No se pudo matricular ningún estudiante.</strong></div><hr style="margin: 10px 0;">' . $mensaje;
        } else {
            $mensaje = '<div><strong>📊 Proceso completado - Resumen:</strong></div><hr style="margin: 10px 0;">' . $mensaje;
        }

        return [
            'success' => count($exitosas) > 0,
            'mensaje' => $mensaje,
            'exitosas' => count($exitosas),
            'duplicadas' => count($duplicadas),
            'errores' => count($errores),
            'total' => $total_procesados
        ];

    } catch (Exception $e) {
        error_log("Error general al crear matrículas: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage(),
            'exitosas' => count($exitosas),
            'duplicadas' => count($duplicadas),
            'errores' => count($errores),
            'total' => $total_procesados
        ];
    }
}

// Mantener función original para compatibilidad
function crearMatricula($conexion, $datos)
{
    try {
        // Verificar si el estudiante ya está matriculado en el curso
        $sqlVerificar = "SELECT COUNT(*) as total FROM matricula 
                        WHERE id_estudiante = :id_estudiante 
                        AND id_curso = :id_curso 
                        AND estado = 'ACTIVA'";

        $stmtVerificar = $conexion->prepare($sqlVerificar);
        $stmtVerificar->bindValue(':id_estudiante', $datos[':id_estudiante']);
        $stmtVerificar->bindValue(':id_curso', $datos[':id_curso']);
        $stmtVerificar->execute();
        $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

        if ($resultado['total'] > 0) {
            return [
                'success' => false,
                'mensaje' => 'El estudiante ya está matriculado en este curso.'
            ];
        }

        // Crear la matrícula
        $sql = "INSERT INTO matricula (id_estudiante, id_curso, fecha_matricula, estado) 
                VALUES (:id_estudiante, :id_curso, :fecha_matricula, :estado)";

        $stmt = $conexion->prepare($sql);
        foreach ($datos as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        if ($stmt->execute()) {
            return [
                'success' => true,
                'mensaje' => 'Matrícula creada exitosamente.',
                'id_matricula' => $conexion->lastInsertId()
            ];
        }
        return [
            'success' => false,
            'mensaje' => 'Error al crear la matrícula.'
        ];
    } catch (PDOException $e) {
        // Verificar si es error de clave foránea
        if ($e->getCode() == 23000) {
            return [
                'success' => false,
                'mensaje' => 'Error: El estudiante o curso seleccionado no existe.'
            ];
        }
        error_log("Error al crear matrícula: " . $e->getMessage());
        return [
            'success' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}
