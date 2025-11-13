<?php
// Limpiar cualquier salida previa
ob_start();

// Configuración inicial
require_once '../../config/database.php';
require_once '../../includes/admin_functions.php';

// Limpiar el buffer de salida
ob_end_clean();

// Verificar que sea una petición AJAX
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> ID del curso no válido</div>';
    exit;
}

try {
    $database = new Database();
    $conexion = $database->connect();

    $cod_curso = intval($_GET['id']);
    $estudiantes = obtenerEstudiantesPorCurso($conexion, $cod_curso);

    if (!$estudiantes || count($estudiantes) === 0) {
        echo '<div class="alert alert-info"><i class="bi bi-info-circle"></i> No hay estudiantes matriculados en este curso.</div>';
        exit;
    }

    // Generar HTML con la tabla
    ?>
    <div class="mb-3">
        <span class="badge bg-primary">Total de estudiantes: <?php echo count($estudiantes); ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Código</th>
                    <th>Nombre Completo</th>
                    <th>Documento</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estudiantes as $estudiante): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($estudiante['cod_estudiante']); ?></td>
                        <td><?php echo htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellidos']); ?></td>
                        <td>
                            <?php 
                            echo htmlspecialchars($estudiante['tipo_documento'] ?? 'N/A'); 
                            echo ': '; 
                            echo htmlspecialchars($estudiante['numero_documento']); 
                            ?>
                        </td>
                        <td><small><?php echo htmlspecialchars($estudiante['correo'] ?? 'N/A'); ?></small></td>
                        <td><?php echo htmlspecialchars($estudiante['telefono'] ?? 'N/A'); ?></td>
                        <td>
                            <?php 
                            $estado = strtoupper($estudiante['estado']);
                            if ($estado === 'ACTIVA' || $estado === 'ACTIVO'): ?>
                                <span class="badge bg-success">✓ ACTIVO</span>
                            <?php elseif ($estado === 'RETIRADO'): ?>
                                <span class="badge bg-warning text-dark">↩ RETIRADO</span>
                            <?php elseif ($estado === 'GRADUADO'): ?>
                                <span class="badge bg-primary">🎓 GRADUADO</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">● <?php echo htmlspecialchars($estado); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php

} catch (Exception $e) {
    error_log("Error al obtener estudiantes del curso: " . $e->getMessage());
    echo '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> Error al cargar los estudiantes del curso.</div>';
}
exit;
