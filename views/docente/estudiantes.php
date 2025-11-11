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
try {
    // Obtener el ID del docente
    $id_docente = obtenerIdDocente($conexion, $_SESSION['usuario']['id']);

    // Obtener la lista de estudiantes asignados al docente
    $estudiantes = obtenerEstudiantesPorDocente($conexion, $id_docente);
    
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
                <div class="alert alert-danger"><?php echo htmlspecialchars($mensaje); ?></div>
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
