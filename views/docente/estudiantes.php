<?php
// Configuración inicial
require_once '../../config/database.php';
require_once '../../includes/docente_functions.php';

// Verificar la sesión
if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol']) !== 'docente') {
    header('Location: ../../index.php');
    exit();
}
$database = new Database();
$conexion = $database->connect();
$mensaje = '';
$tipoMensaje = 'danger';

// Verificar si hay mensaje temporal de redirección
if (isset($_SESSION['mensaje_temp'])) {
    $mensaje = $_SESSION['mensaje_temp'];
    $tipoMensaje = $_SESSION['tipo_mensaje_temp'] ?? 'info';
    unset($_SESSION['mensaje_temp']);
    unset($_SESSION['tipo_mensaje_temp']);
}

// Procesar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modulo = $_POST['modulo'] ?? '';
    
    if ($modulo === 'faltas') {
        $accion = $_POST['accion'] ?? '';
        
        if ($accion === 'registrar_falta') {
            try {
                // Preparar datos para el registro de falta
                $datosRegistro = [
                    ':fecha_registro' => $_POST['fecha_registro'] ?? date('Y-m-d'),
                    ':hora_registro' => $_POST['hora_registro'] ?? date('H:i'),
                    ':id_estudiante' => $_POST['id_estudiante'] ?? 0,
                    ':id_docente' => $_POST['id_docente'] ?? 0,
                    ':id_curso' => $_POST['id_curso'] ?? 0,
                    ':id_tipofalta' => $_POST['id_tipofalta'] ?? 0,
                    ':descripcion_falta' => $_POST['descripcion_falta'] ?? '',
                    ':descargos_estudiante' => $_POST['descargos_estudiante'] ?? '',
                    ':correctivos_disciplinarios' => $_POST['correctivos_disciplinarios'] ?? '',
                    ':compromisos' => $_POST['compromisos'] ?? '',
                    ':observaciones' => $_POST['observaciones'] ?? '',
                    ':estado' => 'REPORTADA'
                ];

                $resultado = registrarFaltaEstudiante($conexion, $datosRegistro);

                if ($resultado['success']) {
                    // Guardar mensaje de éxito en sesión y marcar para redirección
                    $_SESSION['mensaje_temp'] = $resultado['mensaje'];
                    $_SESSION['tipo_mensaje_temp'] = 'success';
                    $_SESSION['redirigir_estudiantes'] = true;
                } else {
                    $mensaje = $resultado['mensaje'];
                    $tipoMensaje = 'danger';
                }
            } catch (Exception $e) {
                $mensaje = 'Error al registrar la falta: ' . $e->getMessage();
                $tipoMensaje = 'danger';
                error_log("Error al registrar falta en estudiantes.php: " . $e->getMessage());
            }
        }
    }
}

try {
    // Obtener el ID del docente
    $id_docente = obtenerIdDocente($conexion, $_SESSION['usuario']['id']);

    // Obtener la lista de estudiantes asignados al docente
    $estudiantes = obtenerEstudiantesPorDocente($conexion, $id_docente);

    //faltas
    $tiposFalta = obtenerTiposFalta($conexion);
    
    // Agrupar estudiantes por curso
    $estudiantesPorCurso = [];
    foreach ($estudiantes as $estudiante) {
        $curso_key = $estudiante['cod_curso'];
        if (!isset($estudiantesPorCurso[$curso_key])) {
            $estudiantesPorCurso[$curso_key] = [
                'nombre_curso' => $estudiante['nombre_curso'],
                'nombre_grado' => $estudiante['nombre_grado'],
                'estudiantes' => []
            ];
        }
        $estudiantesPorCurso[$curso_key]['estudiantes'][] = $estudiante;
    }
} catch (Exception $e) {
    error_log("Error al obtener estudiantes en estudiantes.php: " . $e->getMessage());
    $mensaje = 'Error al cargar los estudiantes. Por favor, inténtelo de nuevo más tarde.';
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">👥 Mis Estudiantes</h2>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
                    <?php 
                    // Permitir HTML en mensajes estructurados
                    if (strpos($mensaje, '<strong>') !== false || strpos($mensaje, '<br>') !== false) {
                        echo $mensaje;
                    } else {
                        echo htmlspecialchars($mensaje);
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (empty($estudiantesPorCurso)): ?>
                <div class="alert alert-info">No tiene estudiantes asignados.</div>
            <?php else: ?>
                <?php foreach ($estudiantesPorCurso as $curso): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-book"></i>
                                <?php echo htmlspecialchars($curso['nombre_grado'] . ' - ' . $curso['nombre_curso']); ?>
                                <span class="badge bg-light text-dark ms-2"><?php echo count($curso['estudiantes']); ?> estudiantes</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Nombre Completo</th>
                                            <th>Tipo Documento</th>
                                            <th>Número Documento</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $contador = 1;
                                        foreach ($curso['estudiantes'] as $estudiante): 
                                        ?>
                                            <tr>
                                                <td><?php echo $contador++; ?></td>
                                                <td><?php echo htmlspecialchars($estudiante['nombre_completo']); ?></td>
                                                <td><?php echo htmlspecialchars($estudiante['tipo_documento']); ?></td>
                                                <td><?php echo htmlspecialchars($estudiante['numero_documento']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning btn-registrar-falta" 
                                                            data-estudiante-id="<?php echo $estudiante['cod_estudiante']; ?>"
                                                            data-estudiante-nombre="<?php echo htmlspecialchars($estudiante['nombre_completo']); ?>"
                                                            data-curso-id="<?php echo $curso_key; ?>"
                                                            data-curso-nombre="<?php echo htmlspecialchars($curso['nombre_grado'] . ' - ' . $curso['nombre_curso']); ?>"
                                                            title="Registrar Falta">
                                                        <i class="bi bi-exclamation-triangle"></i> Registrar Falta
                                                    </button>
                                                    <button class="btn btn-sm btn-info btn-ver-faltas" 
                                                            data-estudiante-id="<?php echo $estudiante['cod_estudiante']; ?>"
                                                            data-estudiante-nombre="<?php echo htmlspecialchars($estudiante['nombre_completo']); ?>"
                                                            data-curso-id="<?php echo $curso_key; ?>"
                                                            title="Ver Historial de Faltas">
                                                        <i class="bi bi-file-text"></i> Ver Faltas
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Registrar Falta -->
<div class="modal fade" id="modalRegistrarFalta" tabindex="-1" aria-labelledby="modalRegistrarFaltaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalRegistrarFaltaLabel">
                    <i class="bi bi-exclamation-triangle"></i> Registrar Falta Disciplinaria
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="modulo" value="faltas">
                    <input type="hidden" name="accion" value="registrar_falta">
                    <input type="hidden" name="id_estudiante" id="falta_id_estudiante">
                    <input type="hidden" name="id_curso" id="falta_id_curso">
                    <input type="hidden" name="id_docente" value="<?php echo $id_docente; ?>">

                    <!-- Información del estudiante -->
                    <div class="alert alert-info">
                        <strong>Estudiante:</strong> <span id="falta_estudiante_nombre"></span><br>
                        <strong>Curso:</strong> <span id="falta_curso_nombre"></span>
                    </div>

                    <!-- Fecha y Hora -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fecha_registro" class="form-label">Fecha del Incidente *</label>
                            <input type="date" class="form-control" id="fecha_registro" name="fecha_registro" 
                                   required value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="hora_registro" class="form-label">Hora del Incidente *</label>
                            <input type="time" class="form-control" id="hora_registro" name="hora_registro" 
                                   required value="<?php echo date('H:i'); ?>">
                        </div>
                    </div>

                    <!-- Tipo de Falta -->
                    <div class="mb-3">
                        <label for="id_tipofalta" class="form-label">Tipo de Falta *</label>
                        <select class="form-select" id="id_tipofalta" name="id_tipofalta" required>
                            <option value="">Seleccione el tipo de falta...</option>
                            <?php if (!empty($tiposFalta)): ?>
                                <?php foreach ($tiposFalta as $tipo): ?>
                                    <option value="<?php echo $tipo['cod_tipofalta']; ?>" 
                                            data-gravedad="<?php echo htmlspecialchars($tipo['gravedad']); ?>">
                                        <?php 
                                        $badge_color = '';
                                        switch ($tipo['gravedad']) {
                                            case 'LEVE': $badge_color = '🟢'; break;
                                            case 'GRAVE': $badge_color = '🟡'; break;
                                            case 'MUY_GRAVE': $badge_color = '🔴'; break;
                                        }
                                        echo $badge_color . ' ' . htmlspecialchars($tipo['nombre_tipo']) . ' - ' . htmlspecialchars($tipo['gravedad']); 
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Seleccione el tipo de falta según su gravedad</small>
                    </div>

                    <!-- Descripción de la Falta -->
                    <div class="mb-3">
                        <label for="descripcion_falta" class="form-label">Descripción de la Falta *</label>
                        <textarea class="form-control" id="descripcion_falta" name="descripcion_falta" 
                                  rows="3" required placeholder="Describa detalladamente la falta cometida..."></textarea>
                        <small class="text-muted">Sea específico sobre lo ocurrido, lugar y circunstancias</small>
                    </div>

                    <!-- Descargos del Estudiante -->
                    <div class="mb-3">
                        <label for="descargos_estudiante" class="form-label">Descargos del Estudiante</label>
                        <textarea class="form-control" id="descargos_estudiante" name="descargos_estudiante" 
                                  rows="2" placeholder="Versión del estudiante sobre los hechos..."></textarea>
                    </div>

                    <!-- Correctivos Disciplinarios -->
                    <div class="mb-3">
                        <label for="correctivos_disciplinarios" class="form-label">Correctivos Disciplinarios Aplicados</label>
                        <textarea class="form-control" id="correctivos_disciplinarios" name="correctivos_disciplinarios" 
                                  rows="2" placeholder="Acciones correctivas aplicadas..."></textarea>
                    </div>

                    <!-- Compromisos -->
                    <div class="mb-3">
                        <label for="compromisos" class="form-label">Compromisos del Estudiante</label>
                        <textarea class="form-control" id="compromisos" name="compromisos" 
                                  rows="2" placeholder="Compromisos adquiridos por el estudiante..."></textarea>
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones Adicionales</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" 
                                  rows="2" placeholder="Observaciones generales..."></textarea>
                    </div>

                    <!-- Estado -->
                    <input type="hidden" name="estado" value="PENDIENTE">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Registrar Falta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Faltas del Estudiante -->
<div class="modal fade" id="modalVerFaltas" tabindex="-1" aria-labelledby="modalVerFaltasLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalVerFaltasLabel">
                    <i class="bi bi-file-text"></i> Historial de Faltas - <span id="ver_faltas_estudiante_nombre"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="contenido_faltas">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando historial de faltas...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['redirigir_estudiantes']) && $_SESSION['redirigir_estudiantes']): ?>
<script>
    // Redirigir usando JavaScript para evitar el reenvío del formulario
    // Esto implementa el patrón PRG (Post-Redirect-Get)
    <?php unset($_SESSION['redirigir_estudiantes']); ?>
    window.location.href = window.location.pathname + window.location.search;
</script>
<?php endif; ?>
