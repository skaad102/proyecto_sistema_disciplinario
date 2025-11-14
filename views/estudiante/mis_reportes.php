<?php
// Verificar si hay una sesión activa y si el usuario es un estudiante
if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol']) !== 'estudiante') {
    header('Location: ../../index.php');
    exit();
}

// Debug de sesión
error_log("Datos de sesión en mis_asignaturas.php: " . print_r($_SESSION, true));

// Incluir archivos necesarios
require_once(__DIR__ . '/../../config/database.php');
require_once(__DIR__ . '/../../includes/estudiante_functions.php');

// Crear conexión a la base de datos
$database = new Database();
$conexion = $database->connect();

try {
    // Debug de la información del usuario
    error_log("Verificando datos de sesión en mis_asignaturas.php");
    error_log("SESSION completa: " . print_r($_SESSION, true));

    // Verificar y obtener el ID del usuario
    if (isset($_SESSION['usuario']['id'])) {
        $id_usuario = $_SESSION['usuario']['id'];
    } else if (isset($_SESSION['usuario']['cod_usuario'])) {
        $id_usuario = $_SESSION['usuario']['cod_usuario'];
    } else {
        throw new Exception("No se pudo encontrar el ID del usuario en la sesión");
    }

    error_log("ID de usuario encontrado: " . $id_usuario);

    // Obtener el ID del estudiante usando la función
    $id_estudiante = obtenerIdEstudiante($conexion, $id_usuario);
    error_log("ID de estudiante obtenido: " . ($id_estudiante ?? 'null'));

    $reportes = obtenerReportesYDetalleEstudianteID($conexion, $id_estudiante);


    if ($id_estudiante === null) {
        throw new Exception("No se encontró el registro de estudiante para este usuario.");
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    die("Error: No se pudo obtener la información del estudiante" . $id_estudiante);
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">📋 Mis Reportes de Faltas</h2>

            <?php if (empty($reportes)): ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-check-circle" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">¡Sin faltas registradas!</h5>
                    <p>No tienes faltas disciplinarias registradas.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Tipo de Falta</th>
                                <th>Gravedad</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportes as $reporte): 
                                // Determinar badge según el estado
                                $estado = strtoupper($reporte['estado']);
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
                                
                                // Determinar color de gravedad
                                $gravedad_badge = 'bg-secondary';
                                $gravedad_icono = '●';
                                switch ($reporte['gravedad']) {
                                    case 'LEVE':
                                        $gravedad_badge = 'bg-success';
                                        $gravedad_icono = '🟢';
                                        break;
                                    case 'GRAVE':
                                        $gravedad_badge = 'bg-warning text-dark';
                                        $gravedad_icono = '🟡';
                                        break;
                                    case 'MUY_GRAVE':
                                        $gravedad_badge = 'bg-danger';
                                        $gravedad_icono = '🔴';
                                        break;
                                }
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reporte['fecha_registro']); ?></td>
                                    <td><?php echo htmlspecialchars($reporte['hora_registro']); ?></td>
                                    <td><?php echo htmlspecialchars($reporte['nombre_tipo']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $gravedad_badge; ?>">
                                            <?php echo $gravedad_icono . ' ' . htmlspecialchars($reporte['gravedad']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $desc = htmlspecialchars($reporte['descripcion_falta']);
                                        echo strlen($desc) > 50 ? substr($desc, 0, 50) . '...' : $desc; 
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $badge_class; ?>">
                                            <?php echo $icono . ' ' . htmlspecialchars($estado); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary btn-ver-detalle-falta-estudiante" 
                                                data-registro-id="<?php echo $reporte['cod_registro']; ?>"
                                                title="Ver Detalle Completo">
                                            <i class="bi bi-eye"></i> Ver Detalle
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Estadísticas -->
                <div class="mt-4">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3><?php echo count($reportes); ?></h3>
                                    <small>Total de Faltas</small>
                                </div>
                            </div>
                        </div>
                        
                        <?php 
                        $por_estado = array_count_values(array_column($reportes, 'estado'));
                        ?>
                        
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
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Ver Detalle de Falta -->
<div class="modal fade" id="modalDetalleFaltaEstudiante" tabindex="-1" aria-labelledby="modalDetalleFaltaEstudianteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDetalleFaltaEstudianteLabel">
                    <i class="bi bi-file-text"></i> Detalle Completo de la Falta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="contenido_detalle_falta_estudiante">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando detalle de la falta...</p>
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