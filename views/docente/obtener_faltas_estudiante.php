<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/docente_functions.php';

// Verificar la sesión
if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol']) !== 'docente') {
    http_response_code(403);
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    exit();
}

// Verificar parámetros
if (!isset($_GET['id_estudiante']) || !isset($_GET['id_curso'])) {
    echo '<div class="alert alert-warning">Faltan parámetros requeridos</div>';
    exit();
}

$id_estudiante = intval($_GET['id_estudiante']);
$id_curso = intval($_GET['id_curso']);

$database = new Database();
$conexion = $database->connect();

try {
    // Obtener el historial de faltas
    $faltas = obtenerReporteEstudiantePorCurso($conexion, $id_curso, $id_estudiante);

    if (empty($faltas)): ?>
        <div class="alert alert-info text-center">
            <i class="bi bi-check-circle" style="font-size: 3rem;"></i>
            <h5 class="mt-3">¡Sin faltas registradas!</h5>
            <p>Este estudiante no tiene faltas registradas en este curso.</p>
        </div>
    <?php else:
        // Contar por estado para estadísticas
        $por_estado = array_count_values(array_column($faltas, 'estado'));
    ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Tipo de Falta</th>
                        <th>Descripción</th>
                        <th>Correctivos</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($faltas as $falta):
                        // Determinar badge según el estado
                        $estado = strtoupper($falta['estado']);
                        $badge_class = 'bg-secondary';
                        $icono = '●';

                        switch ($estado) {
                            case 'REPORTADA':
                                $badge_class = 'bg-warning text-dark';
                                $icono = '🟠';
                                break;
                            case 'EN_PROCESO':
                                $badge_class = 'bg-info text-dark';
                                $icono = '🔵';
                                break;
                            case 'SANCIONADA':
                                $badge_class = 'bg-success';
                                $icono = '🟢';
                                break;
                            case 'ARCHIVADA':
                                $badge_class = 'bg-secondary';
                                $icono = '⚪';
                                break;
                        }
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($falta['fecha_registro']); ?></td>
                            <td><?php echo htmlspecialchars($falta['hora_registro']); ?></td>
                            <td><?php echo htmlspecialchars($falta['nombre_tipo']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-link p-0" data-bs-toggle="collapse"
                                    data-bs-target="#desc_<?php echo $falta['cod_registro']; ?>">
                                    Ver detalles <i class="bi bi-chevron-down"></i>
                                </button>
                                <div class="collapse mt-2" id="desc_<?php echo $falta['cod_registro']; ?>">
                                    <div class="card card-body">
                                        <strong>Descripción:</strong><br>
                                        <?php echo nl2br(htmlspecialchars($falta['descripcion_falta'])); ?>

                                        <?php if (!empty($falta['descargos_estudiante'])): ?>
                                            <hr>
                                            <strong>Descargos del Estudiante:</strong><br>
                                            <?php echo nl2br(htmlspecialchars($falta['descargos_estudiante'])); ?>
                                        <?php endif; ?>

                                        <?php if (!empty($falta['compromisos'])): ?>
                                            <hr>
                                            <strong>Compromisos:</strong><br>
                                            <?php echo nl2br(htmlspecialchars($falta['compromisos'])); ?>
                                        <?php endif; ?>

                                        <?php if (!empty($falta['observaciones'])): ?>
                                            <hr>
                                            <strong>Observaciones:</strong><br>
                                            <?php echo nl2br(htmlspecialchars($falta['observaciones'])); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo nl2br(htmlspecialchars($falta['correctivos_disciplinarios'] ?? 'N/A')); ?></td>
                            <td>
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo $icono . ' ' . htmlspecialchars($estado); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Estadísticas -->
        <div class="mt-3">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h3><?php echo count($faltas); ?></h3>
                            <small>Total de Faltas</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <h3><?php echo $por_estado['REPORTADA'] ?? 0; ?></h3>
                            <small>Reportadas</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h3><?php echo $por_estado['EN_PROCESO'] ?? 0; ?></h3>
                            <small>En Proceso</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h3><?php echo $por_estado['SANCIONADA'] ?? 0; ?></h3>
                            <small>Sancionadas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif;
} catch (Exception $e) {
    error_log("Error al obtener faltas del estudiante: " . $e->getMessage());
    ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i>
        Error al cargar el historial de faltas. Por favor, intente nuevamente.
    </div>
<?php } ?>