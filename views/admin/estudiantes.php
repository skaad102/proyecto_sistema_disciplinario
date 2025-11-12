<?php
// Configuración inicial
require_once '../../config/database.php';
require_once '../../includes/admin_functions.php';

// NO verificar sesión aquí - ya se verifica en index.php
$database = new Database();
$conexion = $database->connect();
$mensaje = '';
$tipoMensaje = 'danger';

// Procesar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar que el formulario sea de ESTUDIANTES
    $modulo = $_POST['modulo'] ?? '';
    
    if ($modulo !== 'estudiantes') {
        // Si no es de estudiantes, no procesar nada aquí
        goto skip_estudiantes_processing;
    }
    
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        try {
            // Preparar datos del usuario
            $datosUsuario = [
                ':id_tipo_documento' => $_POST['id_tipo_documento'] ?? 0,
                ':numero_documento' => $_POST['numero_documento'] ?? '',
                ':nombres' => $_POST['nombres'] ?? '',
                ':apellidos' => $_POST['apellidos'] ?? '',
                ':telefono' => $_POST['telefono'] ?? '',
                ':correo' => $_POST['correo'] ?? '',
                ':direccion' => $_POST['direccion'] ?? '',
                ':usuario' => $_POST['usuario'] ?? '',
                ':clave' => $_POST['clave'] ?? '123456'
            ];

            // Preparar datos del estudiante
            $datosEstudiante = [
                ':fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? ''
            ];

            // Crear usuario y estudiante con transacción
            $resultado = crearUsuarioYEstudiante($conexion, $datosUsuario, $datosEstudiante);

            if ($resultado['success']) {
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = 'success';
            } else {
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = 'danger';
            }
        } catch (Exception $e) {
            $mensaje = 'Error: ' . $e->getMessage();
            $tipoMensaje = 'danger';
            error_log("Error al crear estudiante: " . $e->getMessage());
        }
    }

    if ($accion === 'editar') {
        try {
            $cod_estudiante = $_POST['cod_estudiante'] ?? 0;
            
            // Datos del usuario
            $datosUsuario = [
                ':id_tipo_documento' => $_POST['id_tipo_documento'] ?? 0,
                ':numero_documento' => $_POST['numero_documento'] ?? '',
                ':nombres' => $_POST['nombres'] ?? '',
                ':apellidos' => $_POST['apellidos'] ?? '',
                ':telefono' => $_POST['telefono'] ?? '',
                ':correo' => $_POST['correo'] ?? '',
                ':direccion' => $_POST['direccion'] ?? ''
            ];

            // Datos del estudiante
            $datosEstudiante = [
                ':fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? ''
            ];

            $resultado = actualizarEstudiante($conexion, $datosUsuario, $datosEstudiante, $cod_estudiante);
            if ($resultado) {
                $mensaje = 'Estudiante actualizado exitosamente.';
                $tipoMensaje = 'success';
            } else {
                $mensaje = 'Error al actualizar el estudiante.';
                $tipoMensaje = 'danger';
            }
        } catch (Exception $e) {
            $mensaje = 'Error: ' . $e->getMessage();
            $tipoMensaje = 'danger';
            error_log("Error al editar estudiante: " . $e->getMessage());
        }
    }

    if ($accion === 'desactivar') {
        $cod_estudiante = $_POST['cod_estudiante'] ?? 0;
        $resultado = desactivarEstudiante($conexion, $cod_estudiante);

        if ($resultado['success']) {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'success';
        } else {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'warning';
        }
    }

    if ($accion === 'activar') {
        $cod_estudiante = $_POST['cod_estudiante'] ?? 0;
        $resultado = activarEstudiante($conexion, $cod_estudiante);

        if ($resultado['success']) {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'success';
        } else {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'warning';
        }
    }
}

skip_estudiantes_processing:

try {
    // Obtener la lista de estudiantes
    $estudiantes = obtenerTodosEstudiantes($conexion);
    // Obtener tipos de documento para el formulario
    $tiposDocumento = obtenerTiposDocumento($conexion);
} catch (Exception $e) {
    error_log("Error al obtener estudiantes en estudiantes.php: " . $e->getMessage());
    $mensaje = 'Error al cargar los estudiantes. Por favor, inténtelo de nuevo más tarde.';
    echo 'Debug: Error al obtener estudiantes - ' . $e->getMessage();
}

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>🎓 Gestión de Estudiantes</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearEstudiante">
                    <i class="bi bi-plus-circle"></i> Nuevo Estudiante
                </button>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($estudiantes)): ?>
                <div class="alert alert-info">No hay estudiantes registrados.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Documento</th>
                                <th>Fecha Nacimiento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estudiantes as $estudiante): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($estudiante['cod_estudiante']); ?></td>
                                    <td><?php echo htmlspecialchars($estudiante['nombres']); ?></td>
                                    <td><?php echo htmlspecialchars($estudiante['apellidos']); ?></td>
                                    <td><?php echo htmlspecialchars($estudiante['numero_documento']); ?></td>
                                    <td><?php echo htmlspecialchars($estudiante['fecha_nacimiento'] ?? 'N/A'); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary btn-editar-estudiante"
                                            data-id="<?php echo $estudiante['cod_estudiante']; ?>"
                                            data-fecha="<?php echo htmlspecialchars($estudiante['fecha_nacimiento'] ?? ''); ?>"
                                            data-nombres="<?php echo htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellidos']); ?>"
                                            title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Crear Estudiante -->
<div class="modal fade" id="modalCrearEstudiante" tabindex="-1" aria-labelledby="modalCrearEstudianteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalCrearEstudianteLabel">🎓 Crear Nuevo Usuario Estudiante</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="modulo" value="estudiantes">
                    <input type="hidden" name="accion" value="crear">

                    <h6 class="border-bottom pb-2 mb-3">📄 Información Personal</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_tipo_documento_estudiante" class="form-label">Tipo de Documento *</label>
                            <select class="form-select" id="id_tipo_documento_estudiante" name="id_tipo_documento" required>
                                <option value="">Seleccione...</option>
                                <?php if (!empty($tiposDocumento)): ?>
                                    <?php foreach ($tiposDocumento as $tipo): ?>
                                        <option value="<?php echo htmlspecialchars($tipo['cod_tipodocumento']); ?>">
                                            <?php echo htmlspecialchars($tipo['tipo_documento']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No hay tipos de documento disponibles</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="numero_documento_estudiante" class="form-label">Número de Documento *</label>
                            <input type="text" class="form-control" id="numero_documento_estudiante" name="numero_documento"
                                required maxlength="255" placeholder="Ej: 1234567890">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombres_estudiante" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" id="nombres_estudiante" name="nombres"
                                required maxlength="255" placeholder="Ej: María José">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="apellidos_estudiante" class="form-label">Apellidos *</label>
                            <input type="text" class="form-control" id="apellidos_estudiante" name="apellidos"
                                required maxlength="255" placeholder="Ej: García López">
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-3">📞 Información de Contacto</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono_estudiante" class="form-label">Teléfono *</label>
                            <input type="text" class="form-control" id="telefono_estudiante" name="telefono"
                                required maxlength="255" placeholder="Ej: 3001234567">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="correo_estudiante" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="correo_estudiante" name="correo"
                                required maxlength="255" placeholder="Ej: estudiante@ejemplo.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="direccion_estudiante" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion_estudiante" name="direccion"
                            maxlength="255" placeholder="Ej: Calle 123 #45-67">
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-3">🔐 Credenciales de Acceso</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="usuario_estudiante" class="form-label">Usuario *</label>
                            <input type="text" class="form-control" id="usuario_estudiante" name="usuario"
                                required maxlength="255" placeholder="Ej: maria.garcia">
                            <div class="form-text">Nombre de usuario para iniciar sesión</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="clave_estudiante" class="form-label">Contraseña</label>
                            <input type="text" class="form-control" id="clave_estudiante" name="clave"
                                value="123456" maxlength="255">
                            <div class="form-text">Por defecto: 123456 (se recomienda cambiarla)</div>
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-3">🎓 Información Estudiante</h6>

                    <div class="mb-3">
                        <label for="fecha_nacimiento_crear" class="form-label">Fecha de Nacimiento *</label>
                        <input type="date" class="form-control" id="fecha_nacimiento_crear" name="fecha_nacimiento" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Crear Estudiante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Estudiante -->
<div class="modal fade" id="modalEditarEstudiante" tabindex="-1" aria-labelledby="modalEditarEstudianteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEditarEstudianteLabel">✏️ Editar Estudiante</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="modulo" value="estudiantes">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="cod_estudiante" id="cod_estudiante_editar">

                    <h6 class="border-bottom pb-2 mb-3">📄 Información Personal</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_tipo_documento_editar_estudiante" class="form-label">Tipo de Documento *</label>
                            <select class="form-select" id="id_tipo_documento_editar_estudiante" name="id_tipo_documento" required>
                                <option value="">Seleccione...</option>
                                <?php if (!empty($tiposDocumento)): ?>
                                    <?php foreach ($tiposDocumento as $tipo): ?>
                                        <option value="<?php echo htmlspecialchars($tipo['cod_tipodocumento']); ?>">
                                            <?php echo htmlspecialchars($tipo['tipo_documento']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="numero_documento_editar_estudiante" class="form-label">Número de Documento *</label>
                            <input type="text" class="form-control" id="numero_documento_editar_estudiante" name="numero_documento"
                                required maxlength="255">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombres_editar_estudiante" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" id="nombres_editar_estudiante" name="nombres"
                                required maxlength="255">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="apellidos_editar_estudiante" class="form-label">Apellidos *</label>
                            <input type="text" class="form-control" id="apellidos_editar_estudiante" name="apellidos"
                                required maxlength="255">
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-3">📞 Información de Contacto</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono_editar_estudiante" class="form-label">Teléfono *</label>
                            <input type="text" class="form-control" id="telefono_editar_estudiante" name="telefono"
                                required maxlength="255">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="correo_editar_estudiante" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="correo_editar_estudiante" name="correo"
                                required maxlength="255">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="direccion_editar_estudiante" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion_editar_estudiante" name="direccion" maxlength="255">
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-3">🔐 Usuario de Acceso</h6>

                    <div class="mb-3">
                        <label for="usuario_editar_estudiante" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="usuario_editar_estudiante" readonly>
                        <div class="form-text">No se puede cambiar el nombre de usuario.</div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-3">🎓 Información Estudiante</h6>

                    <div class="mb-3">
                        <label for="fecha_nacimiento_editar" class="form-label">Fecha de Nacimiento *</label>
                        <input type="date" class="form-control" id="fecha_nacimiento_editar" name="fecha_nacimiento" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Actualizar Estudiante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
