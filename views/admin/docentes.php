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
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        try {
            echo "Debug: Iniciando creación de docente\n";
            // Paso 1: Crear el usuario
            $datosUsuario = [
                ':id_tipo_documento' => $_POST['id_tipo_documento'] ?? 0,
                ':numero_documento' => $_POST['numero_documento'] ?? '',
                ':nombres' => $_POST['nombres'] ?? '',
                ':apellidos' => $_POST['apellidos'] ?? '',
                ':telefono' => $_POST['telefono'] ?? '',
                ':correo' => $_POST['correo'] ?? '',
                ':direccion' => $_POST['direccion'] ?? '',
                ':usuario' => $_POST['usuario'] ?? '',
                ':clave' => $_POST['clave'] ?? '123456' // Contraseña por defecto
            ];

            $id_usuario = crearUsuarioDocente($conexion, $datosUsuario);

            if ($id_usuario) {
                // Paso 2: Crear el docente con el id_usuario obtenido
                $datosDocente = [
                    ':id_usuario' => $id_usuario,
                    ':especialidad' => $_POST['especialidad'] ?? ''
                ];

                $resultado = insertarDocente($conexion, $datosDocente);

                if ($resultado) {
                    $mensaje = 'Usuario y docente creados exitosamente.';
                    $tipoMensaje = 'success';
                } else {
                    $mensaje = 'Usuario creado, pero error al registrar como docente.';
                    $tipoMensaje = 'warning';
                }
            } else {
                $mensaje = 'Error al crear el usuario.';
                $tipoMensaje = 'danger';
            }
        } catch (Exception $e) {
            $mensaje = 'Error: ' . $e->getMessage();
            $tipoMensaje = 'danger';
            error_log("Error al crear docente: " . $e->getMessage());
        }
    }

    if ($accion === 'editar') {
        $cod_docente = $_POST['cod_docente'] ?? 0;
        $datos = [
            ':especialidad' => $_POST['especialidad'] ?? ''
        ];

        $resultado = actualizarDocente($conexion, $datos, $cod_docente);
        if ($resultado) {
            $mensaje = 'Docente actualizado exitosamente.';
            $tipoMensaje = 'success';
        } else {
            $mensaje = 'Error al actualizar el docente.';
        }
    }

    if ($accion === 'desactivar') {
        $cod_docente = $_POST['cod_docente'] ?? 0;
        $resultado = desactivarDocente($conexion, $cod_docente);

        if ($resultado['success']) {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'success';
        } else {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'warning';
        }
    }

    if ($accion === 'activar') {
        $cod_docente = $_POST['cod_docente'] ?? 0;
        $resultado = activarDocente($conexion, $cod_docente);

        if ($resultado['success']) {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'success';
        } else {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'warning';
        }
    }
}

try {
    // Obtener la lista de docentes
    $docentes = obtenerTodosDocentes($conexion);
    // Obtener tipos de documento para el formulario
    $tiposDocumento = obtenerTiposDocumento($conexion);
} catch (Exception $e) {
    error_log("Error al obtener docentes en docentes.php: " . $e->getMessage());
    $mensaje = 'Error al cargar los docentes. Por favor, inténtelo de nuevo más tarde.';
    echo 'Debug: Error al obtener docentes - ' . $e->getMessage();
}

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>👨‍🏫 Gestión de Docentes</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
                    <i class="bi bi-plus-circle"></i> Nuevo Docente
                </button>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($docentes)): ?>
                <div class="alert alert-info">No hay docentes registrados.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Documento</th>
                                <th>Especialidad</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($docentes as $docente): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($docente['cod_docente']); ?></td>
                                    <td><?php echo htmlspecialchars($docente['nombres']); ?></td>
                                    <td><?php echo htmlspecialchars($docente['apellidos']); ?></td>
                                    <td><?php echo htmlspecialchars($docente['numero_documento']); ?></td>
                                    <td><?php echo htmlspecialchars($docente['especialidad'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if (strtoupper($docente['estado']) === 'ACTIVO'): ?>
                                            <span class="badge bg-success">ACTIVO</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">INACTIVO</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary btn-editar"
                                            data-id="<?php echo $docente['cod_docente']; ?>"
                                            data-especialidad="<?php echo htmlspecialchars($docente['especialidad'] ?? ''); ?>"
                                            data-nombres="<?php echo htmlspecialchars($docente['nombres'] . ' ' . $docente['apellidos']); ?>"
                                            title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <?php if (strtoupper($docente['estado']) === 'ACTIVO'): ?>
                                            <button class="btn btn-sm btn-warning btn-desactivar"
                                                data-id="<?php echo $docente['cod_docente']; ?>"
                                                data-nombres="<?php echo htmlspecialchars($docente['nombres'] . ' ' . $docente['apellidos']); ?>"
                                                title="Desactivar">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-success btn-activar"
                                                data-id="<?php echo $docente['cod_docente']; ?>"
                                                data-nombres="<?php echo htmlspecialchars($docente['nombres'] . ' ' . $docente['apellidos']); ?>"
                                                title="Activar">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        <?php endif; ?>
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

<!-- Modal Crear Docente -->
<div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalCrearLabel">👨‍🏫 Crear Nuevo Usuario Docente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="crear">

                    <h6 class="border-bottom pb-2 mb-3">📄 Información Personal</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_tipo_documento" class="form-label">Tipo de Documento *</label>
                            <select class="form-select" id="id_tipo_documento" name="id_tipo_documento" required>
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
                            <label for="numero_documento" class="form-label">Número de Documento *</label>
                            <input type="text" class="form-control" id="numero_documento" name="numero_documento"
                                required maxlength="255" placeholder="Ej: 1234567890">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombres" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" id="nombres" name="nombres"
                                required maxlength="255" placeholder="Ej: Juan Carlos">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="apellidos" class="form-label">Apellidos *</label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos"
                                required maxlength="255" placeholder="Ej: Pérez González">
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-3">📞 Información de Contacto</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono *</label>
                            <input type="text" class="form-control" id="telefono" name="telefono"
                                required maxlength="255" placeholder="Ej: 3001234567">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="correo" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="correo" name="correo"
                                required maxlength="255" placeholder="Ej: docente@ejemplo.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion"
                            maxlength="255" placeholder="Ej: Calle 123 #45-67">
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-3">🔐 Credenciales de Acceso</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="usuario" class="form-label">Usuario *</label>
                            <input type="text" class="form-control" id="usuario" name="usuario"
                                required maxlength="255" placeholder="Ej: juan.perez">
                            <div class="form-text">Nombre de usuario para iniciar sesión</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="clave" class="form-label">Contraseña</label>
                            <input type="text" class="form-control" id="clave" name="clave"
                                value="123456" maxlength="255">
                            <div class="form-text">Por defecto: 123456 (se recomienda cambiarla)</div>
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-3">📚 Información Docente</h6>

                    <div class="mb-3">
                        <label for="especialidad_crear" class="form-label">Especialidad</label>
                        <input type="text" class="form-control" id="especialidad_crear" name="especialidad"
                            maxlength="255" placeholder="Ej: Matemáticas, Español, Física, etc.">
                        <div class="form-text">Área o materia principal que enseña el docente</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Crear Docente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Docente -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel">✏️ Editar Docente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="cod_docente" id="cod_docente_editar">

                    <div class="mb-3">
                        <label class="form-label">Docente</label>
                        <input type="text" class="form-control" id="nombres_editar" readonly>
                        <div class="form-text">No se puede cambiar el usuario asociado.</div>
                    </div>

                    <div class="mb-3">
                        <label for="especialidad_editar" class="form-label">Especialidad</label>
                        <input type="text" class="form-control" id="especialidad_editar"
                            name="especialidad" maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Desactivar Docente -->
<div class="modal fade" id="modalDesactivar" tabindex="-1" aria-labelledby="modalDesactivarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalDesactivarLabel">⚠️ Confirmar Desactivación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="desactivar">
                    <input type="hidden" name="cod_docente" id="cod_docente_desactivar">

                    <p>¿Está seguro que desea desactivar al docente?</p>
                    <p class="fw-bold text-warning" id="nombres_desactivar"></p>
                    <p class="text-muted small">El docente no podrá acceder al sistema pero se mantendrá su historial.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Desactivar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Activar Docente -->
<div class="modal fade" id="modalActivar" tabindex="-1" aria-labelledby="modalActivarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalActivarLabel">✅ Confirmar Activación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="activar">
                    <input type="hidden" name="cod_docente" id="cod_docente_activar">

                    <p>¿Está seguro que desea activar al docente?</p>
                    <p class="fw-bold text-success" id="nombres_activar"></p>
                    <p class="text-muted small">El docente podrá acceder nuevamente al sistema.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Activar</button>
                </div>
            </form>
        </div>
    </div>
</div>