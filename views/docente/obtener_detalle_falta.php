<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/docente_functions.php';

// Verificar la sesión
if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol']) !== 'docente') {
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
    // Obtener el detalle de la falta
    $falta = obtenerFaltaPorId($conexion, $id_registro);
    
    if (empty($falta)): ?>
        <div class="alert alert-warning text-center">
            <i class="bi bi-exclamation-triangle" style="font-size: 3rem;"></i>
            <h5 class="mt-3">Falta no encontrada</h5>
            <p>No se encontró el registro solicitado.</p>
        </div>
    <?php else:
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
                $badge_class = 'bg-success';
                $icono = '🟢';
                break;
            case 'ARCHIVADA':
                $badge_class = 'bg-secondary';
                $icono = '⚪';
                break;
        }
    ?>
        <!-- Información del Estudiante -->
        <div class="card mb-3 border-primary">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-person"></i> Información del Estudiante</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Nombre:</strong> <?php echo htmlspecialchars($falta['nombre_estudiante']); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Estado:</strong> 
                        <span class="badge <?php echo $badge_class; ?>">
                            <?php echo $icono . ' ' . htmlspecialchars($estado); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Incidente -->
        <div class="card mb-3 border-warning">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="bi bi-calendar-event"></i> Información del Incidente</h6>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <strong>Fecha:</strong> <?php echo htmlspecialchars($falta['fecha_registro']); ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Hora:</strong> <?php echo htmlspecialchars($falta['hora_registro']); ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Tipo de Falta:</strong> <?php echo htmlspecialchars($falta['nombre_tipo']); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Descripción de la Falta -->
        <div class="card mb-3 border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Descripción de la Falta</h6>
            </div>
            <div class="card-body">
                <?php echo nl2br(htmlspecialchars($falta['descripcion_falta'])); ?>
            </div>
        </div>

        <?php if (!empty($falta['descargos_estudiante'])): ?>
            <!-- Descargos del Estudiante -->
            <div class="card mb-3 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-chat-left-quote"></i> Descargos del Estudiante</h6>
                </div>
                <div class="card-body">
                    <?php echo nl2br(htmlspecialchars($falta['descargos_estudiante'])); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($falta['correctivos_disciplinarios'])): ?>
            <!-- Correctivos Disciplinarios -->
            <div class="card mb-3 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="bi bi-clipboard-check"></i> Correctivos Disciplinarios Aplicados</h6>
                </div>
                <div class="card-body">
                    <?php echo nl2br(htmlspecialchars($falta['correctivos_disciplinarios'])); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($falta['compromisos'])): ?>
            <!-- Compromisos -->
            <div class="card mb-3 border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-hand-thumbs-up"></i> Compromisos del Estudiante</h6>
                </div>
                <div class="card-body">
                    <?php echo nl2br(htmlspecialchars($falta['compromisos'])); ?>
                </div>
            </div>
        <?php endif; ?>

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
