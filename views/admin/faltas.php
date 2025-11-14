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
    // Verificar que el formulario sea de ESTUDIANTES
    $modulo = $_POST['modulo'] ?? '';

    if ($modulo !== 'faltas') {
        // Si no es de faltas, no procesar nada aquí
        goto skip_faltas_processing;
    }
}
skip_faltas_processing:

try {
    // Obtener la lista de estudiantes
    $estudiantes = obtenerTodosEstudiantes($conexion);
} catch (Exception $e) {
    error_log("Error al obtener estudiantes en estudiantes.php: " . $e->getMessage());
    $mensaje = 'Error al cargar los estudiantes. Por favor, inténtelo de nuevo más tarde.';
    echo 'Debug: Error al obtener estudiantes - ' . $e->getMessage();
}

?>

<div class="container mt-4">
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?php echo $tipoMensaje; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Gestión de Faltas de Estudiantes</h5>
        </div>
        <div class="card-body">
            <!-- Buscador -->
            <div class="mb-3">
                <input type="text" id="buscadorEstudiantes" class="form-control" placeholder="Buscar por nombre, apellido o número de documento...">
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Número de Documento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaEstudiantes">
                        <?php if (empty($estudiantes)): ?>
                            <tr class="no-results">
                                <td colspan="3" class="text-center">No hay estudiantes registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($estudiantes as $estudiante): ?>
                                <tr class="fila-estudiante" 
                                    data-nombre="<?php echo strtolower(htmlspecialchars($estudiante['nombres'])); ?>"
                                    data-apellido="<?php echo strtolower(htmlspecialchars($estudiante['apellidos'])); ?>"
                                    data-documento="<?php echo htmlspecialchars($estudiante['numero_documento']); ?>">
                                    <td><?php echo htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellidos']); ?></td>
                                    <td><?php echo htmlspecialchars($estudiante['numero_documento']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary btn-ver-faltas-estudiante" 
                                                data-id="<?php echo $estudiante['cod_estudiante']; ?>"
                                                data-nombre="<?php echo htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellidos']); ?>">
                                            <i class="bi bi-eye"></i> Ver Faltas
                                        </button>
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

<!-- Modal para ver faltas y detalles -->
<div class="modal fade" id="modalFaltasEstudiante" tabindex="-1" aria-labelledby="tituloFaltasEstudiante" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloFaltasEstudiante"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="contenidoFaltasEstudiante">
                <!-- Contenido dinámico: lista de faltas o detalle -->
            </div>
        </div>
    </div>
</div>