<?php
// Configuración inicial
require_once '../../config/database.php';
require_once '../../includes/admin_functions.php';

// La sesión ya está validada en index.php

// Obtener ID del estudiante
$id_estudiante = $_GET['id'] ?? null;

if (!$id_estudiante) {
    echo '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> ID de estudiante no proporcionado.</div>';
    exit();
}

try {
    $database = new Database();
    $conexion = $database->connect();
    
    // Obtener información del estudiante
    $estudiante = obtenerEstudiantePorId($conexion, $id_estudiante);
    
    if (!$estudiante) {
        echo '<div class="alert alert-warning"><i class="bi bi-info-circle"></i> Estudiante no encontrado.</div>';
        exit();
    }
    
    // Obtener reportes de faltas del estudiante
    $reportes = obtenerReportesYDetalleEstudianteID($conexion, $id_estudiante);
    
    if (empty($reportes)) {
        echo '<div class="alert alert-info text-center py-4">
                <i class="bi bi-info-circle fs-1"></i>
                <p class="mt-3 mb-0">Este estudiante no tiene faltas registradas.</p>
              </div>';
        exit();
    }
    
    // Calcular estadísticas
    $estadisticas = [
        'total' => count($reportes),
        'REPORTADA' => 0,
        'EN_PROCESO' => 0,
        'SANCIONADA' => 0,
        'ARCHIVADA' => 0,
        'LEVE' => 0,
        'GRAVE' => 0,
        'MUY_GRAVE' => 0
    ];
    
    foreach ($reportes as $reporte) {
        if (isset($estadisticas[$reporte['estado']])) {
            $estadisticas[$reporte['estado']]++;
        }
        if (isset($estadisticas[$reporte['gravedad']])) {
            $estadisticas[$reporte['gravedad']]++;
        }
    }
    
?>

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary mb-0"><?php echo $estadisticas['total']; ?></h3>
                <small class="text-muted">Total Faltas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-warning mb-0"><?php echo $estadisticas['REPORTADA']; ?></h3>
                <small class="text-muted">Reportadas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-info mb-0"><?php echo $estadisticas['EN_PROCESO']; ?></h3>
                <small class="text-muted">En Proceso</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-danger mb-0"><?php echo $estadisticas['SANCIONADA']; ?></h3>
                <small class="text-muted">Sancionadas</small>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de faltas -->
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Tipo de Falta</th>
                <th>Gravedad</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reportes as $reporte): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($reporte['fecha_registro'])); ?></td>
                    <td><?php echo date('H:i', strtotime($reporte['hora_registro'])); ?></td>
                    <td><?php echo htmlspecialchars($reporte['nombre_tipo']); ?></td>
                    <td>
                        <?php
                        $gravedadClass = '';
                        $gravedadIcon = '';
                        switch ($reporte['gravedad']) {
                            case 'LEVE':
                                $gravedadClass = 'bg-success';
                                $gravedadIcon = 'bi-check-circle';
                                break;
                            case 'GRAVE':
                                $gravedadClass = 'bg-warning';
                                $gravedadIcon = 'bi-exclamation-triangle';
                                break;
                            case 'MUY_GRAVE':
                                $gravedadClass = 'bg-danger';
                                $gravedadIcon = 'bi-x-circle';
                                break;
                        }
                        ?>
                        <span class="badge <?php echo $gravedadClass; ?>">
                            <i class="bi <?php echo $gravedadIcon; ?>"></i> <?php echo htmlspecialchars($reporte['gravedad']); ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        $estadoClass = '';
                        switch ($reporte['estado']) {
                            case 'REPORTADA':
                                $estadoClass = 'bg-warning text-dark';
                                break;
                            case 'EN_PROCESO':
                                $estadoClass = 'bg-info text-dark';
                                break;
                            case 'SANCIONADA':
                                $estadoClass = 'bg-danger';
                                break;
                            case 'ARCHIVADA':
                                $estadoClass = 'bg-secondary';
                                break;
                        }
                        ?>
                        <span class="badge <?php echo $estadoClass; ?>"><?php echo htmlspecialchars($reporte['estado']); ?></span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary btn-ver-detalle-falta-admin" 
                                data-registro="<?php echo $reporte['cod_registro']; ?>">
                            <i class="bi bi-eye"></i> Ver Detalle
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
} catch (Exception $e) {
    error_log("Error en obtener_faltas_estudiante_admin.php: " . $e->getMessage());
    echo '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> Error al cargar las faltas del estudiante.</div>';
}
?>
