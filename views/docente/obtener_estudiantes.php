<?php
// Limpiar cualquier salida previa
ob_start();

session_start();
require_once '../../config/database.php';
require_once '../../includes/docente_functions.php';

// Limpiar el buffer de salida
ob_end_clean();

// Verificar la sesión y el rol
if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol']) !== 'docente') {
    error_log("Acceso no autorizado en obtener_estudiantes.php - Usuario: " . (isset($_SESSION['usuario']) ? $_SESSION['usuario']['id'] : 'No hay sesión'));
    echo '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> Acceso no autorizado</div>';
    exit();
}

// Obtener parámetros
$id_curso = isset($_GET['curso']) ? intval($_GET['curso']) : null;
$id_asignatura = isset($_GET['asignatura']) ? intval($_GET['asignatura']) : null;

if (!$id_curso || !$id_asignatura) {
    error_log("Parámetros inválidos en obtener_estudiantes.php - curso: $id_curso, asignatura: $id_asignatura");
    echo '<div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> Parámetros incompletos o inválidos</div>';
    exit();
}

try {
    $database = new Database();
    $conexion = $database->connect();

    // Verificar que el docente tiene acceso a esta asignatura
    $id_docente = obtenerIdDocente($conexion, $_SESSION['usuario']['id']);

    // Verificar la asignación
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM asignacion_docente 
                               WHERE id_docente = :id_docente 
                               AND id_curso = :id_curso 
                               AND id_asignatura = :id_asignatura");
    $stmt->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
    $stmt->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
    $stmt->bindParam(':id_asignatura', $id_asignatura, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->fetchColumn() == 0) {
        error_log("Intento de acceso a curso no asignado - Docente: $id_docente, Curso: $id_curso, Asignatura: $id_asignatura");
        echo '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> No tiene permiso para ver este curso</div>';
        exit();
    }

    // Obtener estudiantes
    $estudiantes = obtenerEstudiantesPorCurso($conexion, $id_curso, $id_asignatura);
    
    if (!$estudiantes || count($estudiantes) === 0) {
        echo '<div class="alert alert-info"><i class="bi bi-info-circle"></i> No hay estudiantes matriculados en este curso.</div>';
        exit();
    }

    // Generar HTML con la tabla de estudiantes
    ?>
    <div class="mb-3">
        <span class="badge bg-primary">Total de estudiantes: <?php echo count($estudiantes); ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Documento</th>
                    <th>Tipo</th>
                    <th>Nombre Completo</th>
                    <th>Estado</th>
                    <th>Fecha Matrícula</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estudiantes as $index => $estudiante): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($estudiante['numero_documento']); ?></td>
                        <td><?php echo htmlspecialchars($estudiante['tipo_documento']); ?></td>
                        <td><?php echo htmlspecialchars($estudiante['nombre_completo']); ?></td>
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
                        <td><?php echo htmlspecialchars($estudiante['fecha_matricula']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php

} catch (Exception $e) {
    error_log("Error en obtener_estudiantes.php: " . $e->getMessage());
    echo '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> Error al cargar los estudiantes del curso.</div>';
}
exit;
