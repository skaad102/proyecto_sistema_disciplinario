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
    // Verificar que el formulario sea de CURSOS
    $modulo = $_POST['modulo'] ?? '';

    if ($modulo !== 'cursos') {
        // Si no es de cursos, no procesar nada aquí
        goto skip_cursos_processing;
    }

    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        try {
            $datos = [
                ':id_grado' => $_POST['id_grado'] ?? 0,
                ':id_director_grupo' => $_POST['id_director_grupo'] ?? 0,
                ':ano_lectivo' => $_POST['ano_lectivo'] ?? date('Y'),
                ':estado' => 'ACTIVO'
            ];

            $resultado = crearCurso($conexion, $datos);

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
            error_log("Error al crear curso: " . $e->getMessage());
        }
    }

    if ($accion === 'editar') {
        try {
            $cod_curso = $_POST['cod_curso'] ?? 0;
            $datos = [
                ':id_grado' => $_POST['id_grado'] ?? 0,
                ':id_director_grupo' => $_POST['id_director_grupo'] ?? 0,
                ':ano_lectivo' => $_POST['ano_lectivo'] ?? date('Y'),
                ':estado' => $_POST['estado'] ?? 'ACTIVO'
            ];

            $resultado = actualizarCurso($conexion, $datos, $cod_curso);

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
            error_log("Error al editar curso: " . $e->getMessage());
        }
    }

    if ($accion === 'desactivar') {
        $cod_curso = $_POST['cod_curso'] ?? 0;
        $resultado = desactivarCurso($conexion, $cod_curso);

        if ($resultado['success']) {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'success';
        } else {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'warning';
        }
    }

    if ($accion === 'activar') {
        $cod_curso = $_POST['cod_curso'] ?? 0;
        $resultado = activarCurso($conexion, $cod_curso);

        if ($resultado['success']) {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'success';
        } else {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'warning';
        }
    }
}

skip_cursos_processing:

try {
    // Obtener datos necesarios
    $cursos = obtenerTodosCursos($conexion);
    $grados = obtenerTodosGrados($conexion);
    $docentes = obtenerTodosDocentes($conexion);
} catch (Exception $e) {
    error_log("Error al obtener datos en curso.php: " . $e->getMessage());
    $mensaje = 'Error al cargar los datos. Por favor, inténtelo de nuevo más tarde.';
}

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>📚 Gestión de Cursos</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearCurso">
                    <i class="bi bi-plus-circle"></i> Nuevo Curso
                </button>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($cursos)): ?>
                <div class="alert alert-info">No hay cursos registrados.</div>
            <?php else: ?>
                <!-- Buscador de tabla
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" id="buscarCurso" 
                               placeholder="Buscar por grado, director o año lectivo...">
                    </div>
                    <small class="text-muted">Búsqueda en tiempo real</small>
                </div> -->

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaCursos">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Grado</th>
                                <th>Director de Grupo</th>
                                <th>Año Lectivo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cursos as $curso): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($curso['cod_curso']); ?></td>
                                    <td><?php echo htmlspecialchars($curso['nombre_grado']); ?></td>
                                    <td><?php echo htmlspecialchars($curso['nombres'] . ' ' . $curso['apellidos']); ?></td>
                                    <td><?php echo htmlspecialchars($curso['ano_lectivo']); ?></td>
                                    <td>
                                        <?php if (strtoupper($curso['estado']) === 'ACTIVO'): ?>
                                            <span class="badge bg-success">ACTIVO</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">INACTIVO</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary btn-editar-curso"
                                            data-id="<?php echo $curso['cod_curso']; ?>"
                                            title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <?php if (strtoupper($curso['estado']) === 'ACTIVO'): ?>
                                            <button class="btn btn-sm btn-warning btn-desactivar-curso"
                                                data-id="<?php echo $curso['cod_curso']; ?>"
                                                data-nombre="<?php echo htmlspecialchars($curso['nombre_grado'] . ' - ' . $curso['ano_lectivo']); ?>"
                                                title="Desactivar">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-success btn-activar-curso"
                                                data-id="<?php echo $curso['cod_curso']; ?>"
                                                data-nombre="<?php echo htmlspecialchars($curso['nombre_grado'] . ' - ' . $curso['ano_lectivo']); ?>"
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

<!-- Modal Crear Curso -->
<div class="modal fade" id="modalCrearCurso" tabindex="-1" aria-labelledby="modalCrearCursoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalCrearCursoLabel">📚 Crear Nuevo Curso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="modulo" value="cursos">
                    <input type="hidden" name="accion" value="crear">

                    <div class="mb-3">
                        <label for="id_grado" class="form-label">Grado *</label>
                        <select class="form-select" id="id_grado" name="id_grado" required>
                            <option value="">Seleccione...</option>
                            <?php if (!empty($grados)): ?>
                                <?php foreach ($grados as $grado): ?>
                                    <option value="<?php echo htmlspecialchars($grado['cod_grado']); ?>">
                                        <?php echo htmlspecialchars($grado['nombre_grado'] . ' - ' . $grado['nivel']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_director_grupo" class="form-label">Director de Grupo *</label>
                        <input type="text" class="form-control mb-2" id="buscar_director" 
                               placeholder="🔍 Buscar por nombre o documento...">
                        <select class="form-select" id="id_director_grupo" name="id_director_grupo" required size="5">
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
                        <small class="text-muted">Escribe para filtrar docentes</small>
                    </div>

                    <div class="mb-3">
                        <label for="ano_lectivo" class="form-label">Año Lectivo *</label>
                        <input type="number" class="form-control" id="ano_lectivo" name="ano_lectivo" 
                               required min="2020" max="2099" value="<?php echo date('Y'); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Crear Curso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Curso -->
<div class="modal fade" id="modalEditarCurso" tabindex="-1" aria-labelledby="modalEditarCursoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarCursoLabel">✏️ Editar Curso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="modulo" value="cursos">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="cod_curso" id="cod_curso_editar">

                    <div class="mb-3">
                        <label for="id_grado_editar" class="form-label">Grado *</label>
                        <select class="form-select" id="id_grado_editar" name="id_grado" required>
                            <option value="">Seleccione...</option>
                            <?php if (!empty($grados)): ?>
                                <?php foreach ($grados as $grado): ?>
                                    <option value="<?php echo htmlspecialchars($grado['cod_grado']); ?>">
                                        <?php echo htmlspecialchars($grado['nombre_grado'] . ' - ' . $grado['nivel']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_director_grupo_editar" class="form-label">Director de Grupo *</label>
                        <input type="text" class="form-control mb-2" id="buscar_director_editar" 
                               placeholder="🔍 Buscar por nombre o documento...">
                        <select class="form-select" id="id_director_grupo_editar" name="id_director_grupo" required size="5">
                            <option value="">Seleccione...</option>
                            <?php if (!empty($docentes)): ?>
                                <?php foreach ($docentes as $docente): ?>
                                    <option value="<?php echo htmlspecialchars($docente['cod_docente']); ?>"
                                            data-nombre="<?php echo htmlspecialchars(strtolower($docente['nombres'] . ' ' . $docente['apellidos'])); ?>"
                                            data-documento="<?php echo htmlspecialchars($docente['numero_documento']); ?>">
                                        <?php echo htmlspecialchars($docente['nombres'] . ' ' . $docente['apellidos'] . ' - ' . $docente['numero_documento']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Escribe para filtrar docentes</small>
                    </div>

                    <div class="mb-3">
                        <label for="ano_lectivo_editar" class="form-label">Año Lectivo *</label>
                        <input type="number" class="form-control" id="ano_lectivo_editar" name="ano_lectivo" 
                               required min="2020" max="2099">
                    </div>

                    <div class="mb-3">
                        <label for="estado_editar" class="form-label">Estado *</label>
                        <select class="form-select" id="estado_editar" name="estado" required>
                            <option value="ACTIVO">ACTIVO</option>
                            <option value="INACTIVO">INACTIVO</option>
                        </select>
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

<!-- Modal Desactivar Curso -->
<div class="modal fade" id="modalDesactivarCurso" tabindex="-1" aria-labelledby="modalDesactivarCursoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalDesactivarCursoLabel">⚠️ Confirmar Desactivación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="modulo" value="cursos">
                    <input type="hidden" name="accion" value="desactivar">
                    <input type="hidden" name="cod_curso" id="cod_curso_desactivar">

                    <p>¿Está seguro que desea desactivar el curso?</p>
                    <p class="fw-bold text-warning" id="nombre_curso_desactivar"></p>
                    <p class="text-muted small">El curso no estará disponible pero se mantendrá su historial.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Desactivar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Activar Curso -->
<div class="modal fade" id="modalActivarCurso" tabindex="-1" aria-labelledby="modalActivarCursoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalActivarCursoLabel">✅ Confirmar Activación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="modulo" value="cursos">
                    <input type="hidden" name="accion" value="activar">
                    <input type="hidden" name="cod_curso" id="cod_curso_activar">

                    <p>¿Está seguro que desea activar el curso?</p>
                    <p class="fw-bold text-success" id="nombre_curso_activar"></p>
                    <p class="text-muted small">El curso volverá a estar disponible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Activar</button>
                </div>
            </form>
        </div>
    </div>
</div>