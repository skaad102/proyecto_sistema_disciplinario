<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/docente_functions.php';
require_once '../../includes/estudiante_functions.php';

// Verificar la sesión
if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol']) !== 'estudiante') {
    http_response_code(403);
    ?>
    <div class="alert alert-danger">Acceso no autorizado</div>
    <?php
    exit();
}

// Verificar parámetros
if (!isset($_GET['id_registro'])) {
    ?>
    <div class="alert alert-warning">Falta el ID del registro</div>
    <?php
    exit();
}

$id_registro = intval($_GET['id_registro']);

$database = new Database();
$conexion = $database->connect();

try {
    // Obtener ID del estudiante de la sesión
    $id_usuario = $_SESSION['usuario']['id'] ?? $_SESSION['usuario']['cod_usuario'] ?? null;
    if (!$id_usuario) {
        throw new Exception("No se encontró el ID del usuario en la sesión");
    }
    
    $id_estudiante_sesion = obtenerIdEstudiante($conexion, $id_usuario);
    if (!$id_estudiante_sesion) {
        throw new Exception("No se encontró el registro de estudiante");
    }
    
    // Obtener el detalle de la falta
    $falta = obtenerFaltaPorId($conexion, $id_registro);
    
    if (empty($falta)): ?>
        <div class="alert alert-warning text-center">
            <i class="bi bi-exclamation-triangle" style="font-size: 3rem;"></i>
            <h5 class="mt-3">Falta no encontrada</h5>
            <p>No se encontró el registro solicitado.</p>
        </div>
    <?php else:
        // Verificar que la falta pertenezca al estudiante que la está consultando
        if ($falta['cod_estudiante'] != $id_estudiante_sesion) {
            ?>
            <div class="alert alert-danger text-center">
                <i class="bi bi-shield-exclamation" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Acceso Denegado</h5>
                <p>No tienes permiso para ver esta falta.</p>
            </div>
            <?php
            exit();
        }
        
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
                $badge_class = 'bg-danger';
                $icono = '🔴';
                break;
            case 'ARCHIVADA':
                $badge_class = 'bg-secondary';
                $icono = '⚪';
                break;
        }
    ?>
        <!-- Información del Estado -->
        <div class="card mb-3 border-primary">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Estado del Reporte</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Fecha:</strong> <?php echo htmlspecialchars($falta['fecha_registro']); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Estado:</strong> 
                        <span class="badge <?php echo $badge_class; ?>">
                            <?php echo $icono . ' ' . htmlspecialchars($estado); ?>
                        </span>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <strong>Hora:</strong> <?php echo htmlspecialchars($falta['hora_registro']); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tipo de Falta -->
        <div class="card mb-3 border-warning">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Tipo de Falta</h6>
            </div>
            <div class="card-body">
                <strong><?php echo htmlspecialchars($falta['nombre_tipo']); ?></strong>
            </div>
        </div>

        <!-- Descripción de la Falta -->
        <div class="card mb-3 border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0"><i class="bi bi-file-text"></i> Descripción de la Falta</h6>
            </div>
            <div class="card-body">
                <?php echo nl2br(htmlspecialchars($falta['descripcion_falta'])); ?>
            </div>
        </div>

        <?php if (!empty($falta['descargos_estudiante'])): ?>
            <!-- Mis Descargos -->
            <div class="card mb-3 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-chat-left-quote"></i> Mis Descargos</h6>
                </div>
                <div class="card-body">
                    <?php echo nl2br(htmlspecialchars($falta['descargos_estudiante'])); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($falta['correctivos_disciplinarios'])): ?>
            <!-- Correctivos Aplicados -->
            <div class="card mb-3 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="bi bi-clipboard-check"></i> Correctivos Disciplinarios Aplicados</h6>
                </div>
                <div class="card-body">
                    <?php echo nl2br(htmlspecialchars($falta['correctivos_disciplinarios'])); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Mis Compromisos (Editable) -->
        <div class="card mb-3 border-success">
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-hand-thumbs-up"></i> Mis Compromisos</h6>
                    <button type="button" class="btn btn-sm btn-light" id="btnEditarCompromisos">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Vista de solo lectura -->
                <div id="compromisos-readonly">
                    <?php if (!empty($falta['compromisos'])): ?>
                        <?php echo nl2br(htmlspecialchars($falta['compromisos'])); ?>
                    <?php else: ?>
                        <em class="text-muted">No hay compromisos registrados aún. Haz clic en "Editar" para agregar tus compromisos.</em>
                    <?php endif; ?>
                </div>
                
                <!-- Formulario de edición (oculto por defecto) -->
                <form id="form-compromisos" style="display: none;" method="POST" action="actualizar_compromisos.php">
                    <input type="hidden" name="id_registro" value="<?php echo $falta['cod_registro']; ?>">
                    <div class="mb-3">
                        <textarea class="form-control" name="compromisos" id="compromisos" rows="4" 
                                  placeholder="Escribe aquí tus compromisos para mejorar tu comportamiento..."><?php echo htmlspecialchars($falta['compromisos'] ?? ''); ?></textarea>
                        <small class="text-muted">Describe las acciones que te comprometes a realizar para mejorar.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-save"></i> Guardar Compromisos
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" id="btnCancelarCompromisos">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                    </div>
                    <div id="mensaje-compromisos" class="mt-2"></div>
                </form>
            </div>
        </div>

        <?php if (!empty($falta['observaciones'])): ?>
            <!-- Observaciones -->
            <div class="card mb-3 border-secondary">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-clipboard"></i> Observaciones Adicionales</h6>
                </div>
                <div class="card-body">
                    <?php echo nl2br(htmlspecialchars($falta['observaciones'])); ?>
                </div>
            </div>
        <?php endif; ?>

    <?php endif;
    
} catch (Exception $e) {
    error_log("Error al obtener detalle de falta: " . $e->getMessage());
    ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> 
        Error al cargar el detalle de la falta. Por favor, intente nuevamente.
    </div>
<?php } ?>
