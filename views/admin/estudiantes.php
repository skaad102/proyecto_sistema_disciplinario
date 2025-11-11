<?php
// Configuración inicial
require_once '../../config/database.php';
require_once '../../includes/estudiante_functions.php';

// Verificar la sesión
if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol']) !== 'docente') {
    header('Location: ../../index.php');
    exit();
}
$database = new Database();
$conexion = $database->connect();
$mensaje = '';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $id_tipo_documento = $_POST['id_tipo_documento'] ?? '';
    $numero_documento = $_POST['numero_documento'] ?? '';
    $nombres = $_POST['nombres'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $clave = $_POST['clave'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $id_curso = $_POST['id_curso'] ?? '';
    $consultaRol = "SELECT cod_rol FROM rol WHERE LOWER(rol) = 'estudiante' LIMIT 1";
    $stmtRol = $conexion->prepare($consultaRol);
    $stmtRol->execute();
    if ($stmtRol->rowCount() > 0) {
        $id_rol = $stmtRol->fetch(PDO::FETCH_ASSOC)['cod_rol'];
    } else {
        error_log("Error: No se encontró el rol de estudiante en la base de datos");
        die('<div class="alert alert-danger">Error: No se encontró el rol de estudiante</div>');
    }

    $estado = 'ACTIVO';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';

    $mensaje = '';

    if (isset($_POST['guardar'])) {
        // Verificar si ya existe el documento
        if (verificarExisteDocumento($conexion, $id_tipo_documento, $numero_documento)) {
            $mensaje = '<div class="alert alert-danger">❌ Ya existe un usuario registrado con este tipo y número de documento</div>';
        } else {
            // Preparar datos del usuario
            $datosUsuario = [
                ':usuario' => $usuario,
                ':clave' => $clave,
                ':id_rol' => $id_rol,
                ':id_tipo_documento' => $id_tipo_documento,
                ':numero_documento' => $numero_documento,
                ':nombres' => $nombres,
                ':apellidos' => $apellidos,
                ':telefono' => $telefono,
                ':correo' => $correo,
                ':direccion' => $direccion
            ];

            // Insertar usuario
            $cod_usuario = insertarUsuario($conexion, $datosUsuario);
            if ($cod_usuario) {
                // Insertar estudiante
                if (insertarEstudiante($conexion, $cod_usuario, $fecha_nacimiento)) {
                    $mensaje = '<div class="alert alert-success">✅ Estudiante registrado correctamente</div>';
                } else {
                    $mensaje = '<div class="alert alert-danger">❌ Error al registrar estudiante</div>';
                }
            } else {
                $mensaje = '<div class="alert alert-danger">❌ Error al registrar usuario</div>';
            }
        }
    } else if (isset($_POST['editar'])) {
        $cod_estudiante = $_POST['cod_estudiante'] ?? '';

        // Verificar si ya existe otro usuario con el mismo tipo y número de documento
        $checkDocumento = "SELECT COUNT(*) FROM usuario u 
                          LEFT JOIN estudiante e ON e.id_usuario = u.cod_usuario
                          WHERE u.id_tipo_documento = :id_tipo_documento 
                          AND u.numero_documento = :numero_documento 
                          AND e.cod_estudiante != :cod_estudiante";
        $stmt = $conexion->prepare($checkDocumento);
        $stmt->bindParam(':id_tipo_documento', $id_tipo_documento, PDO::PARAM_INT);
        $stmt->bindParam(':numero_documento', $numero_documento, PDO::PARAM_STR);
        $stmt->bindParam(':cod_estudiante', $cod_estudiante, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->fetchColumn() > 0) {
            $mensaje = '<div class="alert alert-danger">❌ Ya existe otro usuario registrado con este tipo y número de documento</div>';
        } else {
            try {
                // Preparar datos del usuario para actualización
                $datosUsuario = [
                    ':usuario' => $usuario,
                    ':id_tipo_documento' => intval($id_tipo_documento),
                    ':numero_documento' => $numero_documento,
                    ':nombres' => $nombres,
                    ':apellidos' => $apellidos,
                    ':telefono' => $telefono,
                    ':correo' => $correo,
                    ':direccion' => $direccion
                ];

                // Actualizar usuario usando la función
                if (actualizarUsuario($conexion, $datosUsuario, $cod_estudiante)) {
                    // Actualizar estudiante usando la función
                    if (actualizarEstudiante($conexion, $cod_estudiante, $fecha_nacimiento, intval($id_curso))) {
                        $mensaje = '<div class="alert alert-success">✅ Estudiante actualizado correctamente</div>';
                    } else {
                        $mensaje = '<div class="alert alert-danger">❌ Error al actualizar estudiante</div>';
                    }
                } else {
                    $mensaje = '<div class="alert alert-danger">❌ Error al actualizar usuario</div>';
                }
            } catch (Exception $e) {
                error_log("Error al actualizar estudiante: " . $e->getMessage());
            }
        }
    } else if (isset($_POST['eliminar'])) {
        $cod_estudiante = $_POST['cod_estudiante'] ?? '';

        // Usar la función de eliminación
        $resultado = eliminarEstudiante($conexion, $cod_estudiante);

        if ($resultado['success']) {
            $mensaje = '<div class="alert alert-success">✅ ' . $resultado['mensaje'] . '</div>';
        } else {
            $mensaje = '<div class="alert alert-danger">❌ ' . $resultado['mensaje'] . '</div>';
        }
    }
}

// Consultar estudiantes
$estudiantes = [];
try {
    // Consulta principal de estudiantes con manejo de errores mejorado
    $consulta = "SELECT 
        e.cod_estudiante,
        e.id_usuario,
        e.fecha_nacimiento,
        u.cod_usuario,
        u.id_tipo_documento,
        u.numero_documento,
        u.nombres,
        u.apellidos,
        u.telefono,
        u.correo,
        u.direccion,
        u.usuario,
        td.tipo_documento as nombre_tipo_documento
    FROM estudiante e
    INNER JOIN usuario u ON e.id_usuario = u.cod_usuario
    LEFT JOIN tipo_documento td ON u.id_tipo_documento = td.cod_tipodocumento
    ORDER BY e.cod_estudiante DESC";

    // Preparar y ejecutar la consulta con manejo de errores
    $stmt = $conexion->prepare($consulta);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . implode(" ", $conexion->errorInfo()));
    }

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . implode(" ", $stmt->errorInfo()));
    }

    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($estudiantes === false) {
        throw new Exception("Error al obtener los resultados: " . implode(" ", $stmt->errorInfo()));
    }
} catch (Exception $e) {
    error_log("Error en estudiantes.php: " . $e->getMessage());
    echo '<div class="alert alert-danger">Error al cargar los estudiantes. Por favor, revise los logs para más detalles.</div>';
    echo $e->getMessage();
    $estudiantes = [];
}
?>

<div class="container mt-4">
    <?php if (isset($mensaje)) echo $mensaje; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Registro de Estudiantes</h5>
        </div>
        <div class="card-body">
            <form method="POST" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cod_estudiante" class="form-label">Código estudiante:</label>
                        <input type="text" class="form-control" name="cod_estudiante" id="cod_estudiante" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="id_tipo_documento" class="form-label">Tipo de documento:</label>
                        <select class="form-select" name="id_tipo_documento" id="id_tipo_documento" required>
                            <option value="">Seleccione...</option>
                            <?php
                            $tiposDocumento = obtenerTiposDocumento($conexion);
                            foreach ($tiposDocumento as $tipo) {
                                echo "<option value='{$tipo['cod_tipodocumento']}'>{$tipo['tipo_documento']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="numero_documento" class="form-label">Número de Documento:</label>
                        <input type="text" class="form-control" name="numero_documento" id="numero_documento" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nombres" class="form-label">Nombres:</label>
                        <input type="text" class="form-control" name="nombres" id="nombres" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="apellidos" class="form-label">Apellidos:</label>
                        <input type="text" class="form-control" name="apellidos" id="apellidos" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento:</label>
                        <input type="date" class="form-control" name="fecha_nacimiento" id="fecha_nacimiento" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="id_curso" class="form-label">Curso:</label>
                        <select class="form-select" name="id_curso" id="id_curso" required>
                            <option value="">Seleccione un curso...</option>
                            <?php
                            $cursos = obtenerCursos($conexion);
                            foreach ($cursos as $curso) {
                                echo "<option value='{$curso['cod_curso']}'>{$curso['nombre_curso']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="telefono" class="form-label">Teléfono:</label>
                        <input type="tel" class="form-control" name="telefono" id="telefono" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="correo" class="form-label">Correo electrónico:</label>
                        <input type="email" class="form-control" name="correo" id="correo" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="direccion" class="form-label">Dirección:</label>
                        <input type="text" class="form-control" name="direccion" id="direccion" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="usuario" class="form-label">Usuario:</label>
                        <input type="text" class="form-control" name="usuario" id="usuario" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="clave" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control" name="clave" id="clave">
                        <small class="text-muted">Dejar en blanco para mantener la contraseña actual al editar</small>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="guardar" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                    <button type="submit" name="editar" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                    <button type="submit" name="eliminar" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este estudiante?')">
                        <i class="bi bi-trash"></i> Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Lista de Estudiantes</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Tipo Doc.</th>
                            <th>Documento</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Fecha Nac.</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Dirección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estudiantes as $estudiante): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($estudiante['cod_estudiante']); ?></td>
                                <td><?php echo htmlspecialchars($estudiante['nombre_tipo_documento']); ?></td>
                                <td><?php echo htmlspecialchars($estudiante['numero_documento']); ?></td>
                                <td><?php echo htmlspecialchars($estudiante['nombres']); ?></td>
                                <td><?php echo htmlspecialchars($estudiante['apellidos']); ?></td>
                                <td><?php echo htmlspecialchars($estudiante['fecha_nacimiento']); ?></td>
                                <td><?php echo htmlspecialchars($estudiante['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($estudiante['correo']); ?></td>
                                <td><?php echo htmlspecialchars($estudiante['direccion']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="cargarEstudiante(<?php echo htmlspecialchars(json_encode($estudiante)); ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function cargarEstudiante(estudiante) {
        document.getElementById('cod_estudiante').value = estudiante.cod_estudiante;
        document.getElementById('id_tipo_documento').value = estudiante.id_tipo_documento;
        document.getElementById('numero_documento').value = estudiante.numero_documento;
        document.getElementById('nombres').value = estudiante.nombres;
        document.getElementById('apellidos').value = estudiante.apellidos;
        document.getElementById('fecha_nacimiento').value = estudiante.fecha_nacimiento;
        document.getElementById('telefono').value = estudiante.telefono;
        document.getElementById('correo').value = estudiante.correo;
        document.getElementById('direccion').value = estudiante.direccion;
        document.getElementById('usuario').value = estudiante.usuario;
        document.getElementById('id_curso').value = estudiante.id_curso;
        // No cargamos la clave por seguridad
        document.getElementById('clave').value = '';
    }

    // Validación de formularios Bootstrap
    (function() {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>