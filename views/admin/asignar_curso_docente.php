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
    // Verificar que el formulario sea de ASIGNACIONES
    $modulo = $_POST['modulo'] ?? '';

    if ($modulo !== 'asignaciones') {
        // Si no es de asignaciones, no procesar nada aquí
        goto skip_asignaciones_processing;
    }

    $accion = $_POST['accion'] ?? '';

    if ($accion === 'asignar') {
        try {
            $datos = [
                ':id_docente' => $_POST['id_docente'] ?? 0,
                ':id_curso' => $_POST['id_curso'] ?? 0,
                ':id_asignatura' => $_POST['id_asignatura'] ?? 0,
                ':ano_lectivo' => $_POST['ano_lectivo'] ?? date('Y')
            ];

            $resultado = asignarAsignaturaADocente($conexion, $datos);

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
            error_log("Error al asignar asignatura: " . $e->getMessage());
        }
    }

    if ($accion === 'eliminar') {
        $cod_asignacion = $_POST['cod_asignacion'] ?? 0;
        $resultado = eliminarAsignacion($conexion, $cod_asignacion);

        if ($resultado['success']) {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'success';
        } else {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'warning';
        }
    }
}

skip_asignaciones_processing:

try {
    // Obtener datos necesarios
    $asignaciones = onternerTodasAsignaciones($conexion);
    $docentes = obtenerTodosDocentes($conexion);
    $cursos = obtenerTodosCursos($conexion);
    $asignaturas = obtenerTodasAsignaturas($conexion);
} catch (Exception $e) {
    error_log("Error al obtener datos en asignar_curso_docente.php: " . $e->getMessage());
    $mensaje = 'Error al cargar los datos. Por favor, inténtelo de nuevo más tarde.';
}

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>📋 Asignación de Asignaturas a Docentes</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAsignarAsignatura">
                    <i class="bi bi-plus-circle"></i> Nueva Asignación
                </button>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($asignaciones)): ?>
                <div class="alert alert-info">No hay asignaciones registradas.</div>
            <?php else: ?>
                <!-- Buscador -->
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" id="buscarAsignacion"
                            placeholder="Buscar por docente, curso o asignatura...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaAsignaciones">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Docente</th>
                                <th>Curso</th>
                                <th>Asignatura</th>
                                <th>Año Lectivo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asignaciones as $asignacion): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($asignacion['cod_asignacion']); ?></td>
                                    <td><?php echo htmlspecialchars($asignacion['nombres'] . ' ' . $asignacion['apellidos']); ?></td>
                                    <td><?php echo htmlspecialchars($asignacion['nombre_grado']); ?></td>
                                    <td><?php echo htmlspecialchars($asignacion['nombre_asignatura']); ?></td>
                                    <td><?php echo htmlspecialchars($asignacion['ano_lectivo']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger btn-eliminar-asignacion"
                                            data-id="<?php echo $asignacion['cod_asignacion']; ?>"
                                            data-info="<?php echo htmlspecialchars($asignacion['nombres'] . ' ' . $asignacion['apellidos'] . ' - ' . $asignacion['nombre_asignatura']); ?>"
                                            title="Eliminar">
                                            <i class="bi bi-trash"></i>
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

<!-- Modal Asignar Asignatura -->
<div class="modal fade" id="modalAsignarAsignatura" tabindex="-1" aria-labelledby="modalAsignarAsignaturaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalAsignarAsignaturaLabel">📋 Asignar Asignatura a Docente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="modulo" value="asignaciones">
                    <input type="hidden" name="accion" value="asignar">

                    <div class="mb-3">
                        <label for="id_docente_asignar" class="form-label">Docente *</label>
                        <input type="text" class="form-control mb-2" id="buscar_docente_asignar"
                            placeholder="🔍 Buscar por nombre o documento...">
                        <select class="form-select" id="id_docente_asignar" name="id_docente" required size="5">
                            <option value="">Seleccione...</option>
                            <?php if (!empty($docentes)): ?>
                                <?php foreach ($docentes as $docente): ?>
                                    <?php if (strtoupper($docente['estado']) === 'ACTIVO'): ?>
                                        <option value="<?php echo htmlspecialchars($docente['cod_docente']); ?>"
                                            data-nombre="<?php echo htmlspecialchars(strtolower($docente['nombres'] . ' ' . $docente['apellidos'])); ?>"
                                            data-documento="<?php echo htmlspecialchars($docente['numero_documento']); ?>">
                                            <?php echo htmlspecialchars($docente['nombres'] . ' ' . $docente['apellidos'] . ' - ' . $docente['numero_documento']); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Seleccione el docente que dictará la asignatura</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_curso_asignar" class="form-label">Curso *</label>
                            <select class="form-select" id="id_curso_asignar" name="id_curso" required>
                                <option value="">Seleccione...</option>
                                <?php if (!empty($cursos)): ?>
                                    <?php foreach ($cursos as $curso): ?>
                                        <?php if (strtoupper($curso['estado']) === 'ACTIVO'): ?>
                                            <option value="<?php echo htmlspecialchars($curso['cod_curso']); ?>">
                                                <?php echo htmlspecialchars($curso['nombre_grado'] . ' - ' . $curso['ano_lectivo']); ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="id_asignatura_asignar" class="form-label">Asignatura *</label>
                            <select class="form-select" id="id_asignatura_asignar" name="id_asignatura" required>
                                <option value="">Seleccione...</option>
                                <?php if (!empty($asignaturas)): ?>
                                    <?php foreach ($asignaturas as $asignatura): ?>
                                        <option value="<?php echo htmlspecialchars($asignatura['cod_asignatura']); ?>">
                                            <?php echo htmlspecialchars($asignatura['nombre_asignatura']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="ano_lectivo_asignar" class="form-label">Año Lectivo *</label>
                        <input type="number" class="form-control" id="ano_lectivo_asignar" name="ano_lectivo"
                            required min="2020" max="2099" value="<?php echo date('Y'); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Asignar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar Asignación -->
<div class="modal fade" id="modalEliminarAsignacion" tabindex="-1" aria-labelledby="modalEliminarAsignacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarAsignacionLabel">⚠️ Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="modulo" value="asignaciones">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="cod_asignacion" id="cod_asignacion_eliminar">

                    <p>¿Está seguro que desea eliminar esta asignación?</p>
                    <p class="fw-bold text-danger" id="info_asignacion_eliminar"></p>
                    <p class="text-muted small">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>