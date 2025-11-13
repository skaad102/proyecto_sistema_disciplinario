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
    $asignaturas = obtenerAsignaturasDocente($conexion, $id_docente);
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
            <h5 class="card-title mb-0">Mis Asignaturas Asignadas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Grado</th>
                            <th>Curso</th>
                            <th>Asignatura</th>
                            <th>Año Lectivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($asignaturas)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No hay asignaturas asignadas</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($asignaturas as $asignatura): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($asignatura['nombre_grado']); ?></td>
                                    <td><?php echo htmlspecialchars($asignatura['nombre_curso']); ?></td>
                                    <td><?php echo htmlspecialchars($asignatura['nombre_asignatura']); ?></td>
                                    <td><?php echo htmlspecialchars($asignatura['ano_lectivo']); ?></td>
                                    <td>
                                        <button type="button"
                                            class="btn btn-sm btn-primary ver-estudiantes"
                                            data-bs-toggle="modal"
                                            data-bs-target="#estudiantesModal"
                                            data-curso="<?php echo $asignatura['id_curso']; ?>"
                                            data-asignatura="<?php echo $asignatura['id_asignatura']; ?>"
                                            data-nombre-curso="<?php echo htmlspecialchars($asignatura['nombre_curso']); ?>"
                                            data-nombre-asignatura="<?php echo htmlspecialchars($asignatura['nombre_asignatura']); ?>">
                                            <i class="bi bi-people"></i> Ver Estudiantes
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

<!-- Modal para mostrar estudiantes -->
<div class="modal fade" id="estudiantesModal" tabindex="-1" aria-labelledby="estudiantesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="estudiantesModalLabel">Estudiantes del Curso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center" id="loadingSpinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p>Cargando estudiantes...</p>
                </div>
                <div id="estudiantesList" class="table-responsive">
                    <!-- Aquí se cargarán los estudiantes dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ocultar el spinner inicialmente
        document.getElementById('loadingSpinner').style.display = 'none';

        // Manejar el clic en el botón "Ver Estudiantes"
        const buttons = document.querySelectorAll('.ver-estudiantes');
        buttons.forEach(button => {
            button.addEventListener('click', async function() {
                const cursoId = this.dataset.curso;
                const asignaturaId = this.dataset.asignatura;
                const nombreCurso = this.dataset.nombreCurso;
                const nombreAsignatura = this.dataset.nombreAsignatura;

                // Actualizar el título del modal
                document.getElementById('estudiantesModalLabel').textContent =
                    `Estudiantes - ${nombreCurso} - ${nombreAsignatura}`;

                // Mostrar el spinner y limpiar contenido previo
                document.getElementById('loadingSpinner').style.display = 'block';
                document.getElementById('estudiantesList').innerHTML = '';

                try {
                    // Cargar los estudiantes (ahora devuelve HTML)
                    const response = await fetch(`obtener_estudiantes.php?curso=${cursoId}&asignatura=${asignaturaId}`);
                    
                    if (!response.ok) {
                        throw new Error(`Error HTTP: ${response.status}`);
                    }
                    
                    // Obtener el HTML generado por PHP
                    const html = await response.text();
                    
                    // Ocultar el spinner
                    document.getElementById('loadingSpinner').style.display = 'none';
                    
                    // Insertar el HTML en el contenedor
                    document.getElementById('estudiantesList').innerHTML = html;

                } catch (error) {
                    console.error('Error en la carga de estudiantes:', error);
                    document.getElementById('loadingSpinner').style.display = 'none';
                    
                    document.getElementById('estudiantesList').innerHTML = `
                        <div class="alert alert-danger">
                            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Error</h6>
                            <p class="mb-0">Error al cargar los estudiantes. Detalle: ${error.message}</p>
                            <hr>
                            <p class="mb-0">Por favor, intente nuevamente o contacte al administrador.</p>
                        </div>
                    `;
                }
            });
        });
    });
</script>