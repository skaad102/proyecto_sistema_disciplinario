<?php
// Configuración inicial
require_once '../../config/database.php';
require_once '../../includes/admin_functions.php';

// Verificar la sesión
if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol']) !== 'directivo') {
    header('Location: ../../index.php');
    exit();
}
$database = new Database();
$conexion = $database->connect();
$mensaje = '';
$tipoMensaje = 'danger';

// Procesar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'crear') {
        $datos = [
            ':nombre_asignatura' => $_POST['nombre_asignatura'] ?? '',
            ':descripcion' => $_POST['descripcion'] ?? ''
        ];
        
        $resultado = insertarAsignatura($conexion, $datos);
        if ($resultado) {
            $mensaje = 'Asignatura creada exitosamente.';
            $tipoMensaje = 'success';
        } else {
            $mensaje = 'Error al crear la asignatura.';
        }
    }
    
    if ($accion === 'editar') {
        $cod_asignatura = $_POST['cod_asignatura'] ?? 0;
        $datos = [
            ':nombre_asignatura' => $_POST['nombre_asignatura'] ?? '',
            ':descripcion' => $_POST['descripcion'] ?? ''
        ];
        
        $resultado = actualizarAsignatura($conexion, $datos, $cod_asignatura);
        if ($resultado) {
            $mensaje = 'Asignatura actualizada exitosamente.';
            $tipoMensaje = 'success';
        } else {
            $mensaje = 'Error al actualizar la asignatura.';
        }
    }
    
    if ($accion === 'eliminar') {
        $cod_asignatura = $_POST['cod_asignatura'] ?? 0;
        $resultado = eliminarAsignatura($conexion, $cod_asignatura);
        
        if ($resultado['success']) {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'success';
        } else {
            $mensaje = $resultado['mensaje'];
            $tipoMensaje = 'warning';
        }
    }
}

try {
    // Obtener la lista de asignaturas
    $asignaturas = obtenerTodasAsignaturas($conexion);
} catch (Exception $e) {
    error_log("Error al obtener asignaturas en asignaturas.php: " . $e->getMessage());
    $mensaje = 'Error al cargar las asignaturas. Por favor, inténtelo de nuevo más tarde.';
}

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>📚 Gestión de Asignaturas</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrear">
                    <i class="bi bi-plus-circle"></i> Nueva Asignatura
                </button>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($asignaturas)): ?>
                <div class="alert alert-info">No hay asignaturas registradas.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Nombre de la Asignatura</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asignaturas as $asignatura): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($asignatura['cod_asignatura']); ?></td>
                                    <td><?php echo htmlspecialchars($asignatura['nombre_asignatura']); ?></td>
                                    <td><?php echo htmlspecialchars($asignatura['descripcion'] ?? 'N/A'); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary btn-editar" 
                                                data-id="<?php echo $asignatura['cod_asignatura']; ?>"
                                                data-nombre="<?php echo htmlspecialchars($asignatura['nombre_asignatura']); ?>"
                                                data-descripcion="<?php echo htmlspecialchars($asignatura['descripcion'] ?? ''); ?>"
                                                title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-eliminar" 
                                                data-id="<?php echo $asignatura['cod_asignatura']; ?>"
                                                data-nombre="<?php echo htmlspecialchars($asignatura['nombre_asignatura']); ?>"
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Crear Asignatura -->
<div class="modal fade" id="modalCrear" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearLabel">📚 Nueva Asignatura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="crear">
                    
                    <div class="mb-3">
                        <label for="nombre_asignatura_crear" class="form-label">Nombre de la Asignatura *</label>
                        <input type="text" class="form-control" id="nombre_asignatura_crear" 
                               name="nombre_asignatura" required maxlength="255">
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion_crear" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion_crear" 
                                  name="descripcion" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Asignatura -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel">✏️ Editar Asignatura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="cod_asignatura" id="cod_asignatura_editar">
                    
                    <div class="mb-3">
                        <label for="nombre_asignatura_editar" class="form-label">Nombre de la Asignatura *</label>
                        <input type="text" class="form-control" id="nombre_asignatura_editar" 
                               name="nombre_asignatura" required maxlength="255">
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion_editar" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion_editar" 
                                  name="descripcion" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar Asignatura -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarLabel">⚠️ Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="cod_asignatura" id="cod_asignatura_eliminar">
                    
                    <p>¿Está seguro que desea eliminar la asignatura?</p>
                    <p class="fw-bold text-danger" id="nombre_asignatura_eliminar"></p>
                    <p class="text-muted small">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Editar asignatura
document.querySelectorAll('.btn-editar').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const nombre = this.dataset.nombre;
        const descripcion = this.dataset.descripcion;
        
        document.getElementById('cod_asignatura_editar').value = id;
        document.getElementById('nombre_asignatura_editar').value = nombre;
        document.getElementById('descripcion_editar').value = descripcion;
        
        const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
        modal.show();
    });
});

// Eliminar asignatura
document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const nombre = this.dataset.nombre;
        
        document.getElementById('cod_asignatura_eliminar').value = id;
        document.getElementById('nombre_asignatura_eliminar').textContent = nombre;
        
        const modal = new bootstrap.Modal(document.getElementById('modalEliminar'));
        modal.show();
    });
});
</script>
