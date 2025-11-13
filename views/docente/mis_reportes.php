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

try {
    // Obtener el ID del docente usando la función centralizada
    $id_docente = obtenerIdDocente($conexion, $_SESSION['usuario']['id']);
    // Obtener las asignaturas asignadas al docente
    $mis_reportes = obtenerTodasFaltasDocente($conexion, $id_docente);
} catch (Exception $e) {
    error_log("Error en mis_asignaturas.php: " . $e->getMessage());
    $error = "Error al cargar las asignaturas. Por favor, intente nuevamente.";
}
?>

<div class="container mt-4">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Mis Reportes</h5>
                <button class="btn btn-light btn-sm" onclick="location.reload();" title="Actualizar lista">
                    <i class="bi bi-arrow-clockwise"></i> Actualizar
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Fecha</th>
                            <th>Tipo Falta</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($mis_reportes)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No hay reportes disponibles</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($mis_reportes as $reporte): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reporte['nombre_estudiante']); ?></td>
                                    <td><?php echo htmlspecialchars($reporte['fecha_registro']); ?></td>
                                    <td><?php echo htmlspecialchars($reporte['nombre_tipo']); ?></td>
                                    <td><?php echo htmlspecialchars($reporte['descripcion_falta']); ?></td>
                                    <td>
                                        <!-- 'REPORTADA', 'EN_PROCESO', 'SANCIONADA', 'ARCHIVADA' -->
                                        <?php
                                        $estado = strtoupper($reporte['estado']);
                                        if ($estado === 'REPORTADA'): ?>
                                            <span class="badge bg-warning text-dark">🟠 REPORTADA</span
                                                <?php elseif ($estado === 'EN_PROCESO'): ?>
                                                <span class="badge bg-info text-dark">🔵 EN PROCESO</span>
                                        <?php elseif ($estado === 'SANCIONADA'): ?>
                                            <span class="badge bg-success">🟢 SANCIONADA</span>
                                        <?php elseif ($estado === 'ARCHIVADA'): ?>
                                            <span class="badge bg-secondary">⚪ ARCHIVADA</span>
                                        <?php else: ?>
                                            <span class="badge bg-dark">● <?php echo htmlspecialchars($estado); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>