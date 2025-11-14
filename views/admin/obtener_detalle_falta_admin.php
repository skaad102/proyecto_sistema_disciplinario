<?php
// Configuración inicial
require_once '../../config/database.php';
require_once '../../includes/docente_functions.php';

// La sesión ya está validada en index.php

// Obtener ID del registro
$id_registro = $_GET['id'] ?? null;

if (!$id_registro) {
    echo '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> ID de registro no proporcionado.</div>';
    exit();
}

try {
    $database = new Database();
    $conexion = $database->connect();
    
    // Obtener detalle de la falta (reutilizamos la función de docente)
    $falta = obtenerFaltaPorId($conexion, $id_registro);
    
    if (!$falta) {
        echo '<div class="alert alert-warning"><i class="bi bi-info-circle"></i> Falta no encontrada.</div>';
        exit();
    }
    
?>

<!-- Botón para volver a la lista -->
<div class="mb-3">
    <button id="btnVolverListaFaltas" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver a la Lista
    </button>
</div>

<!-- Información básica -->
<div class="row mb-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-person"></i> Información del Estudiante</h6>
            </div>
            <div class="card-body">
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($falta['nombre_estudiante']); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-calendar"></i> Fecha y Hora</h6>
            </div>
            <div class="card-body">
                <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($falta['fecha_registro'])); ?></p>
                <p class="mb-0"><strong>Hora:</strong> <?php echo date('H:i', strtotime($falta['hora_registro'])); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Estado y Tipo de Falta -->
<div class="row mb-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="bi bi-flag"></i> Estado (Editable)</h6>
            </div>
            <div class="card-body">
                <form id="formEstadoFalta" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="id_registro" value="<?php echo $falta['cod_registro']; ?>">
                    <select name="estado" id="selectEstado" class="form-select" style="max-width: 300px;">
                        <option value="REPORTADA" <?php echo ($falta['estado'] == 'REPORTADA') ? 'selected' : ''; ?>>
                            📝 REPORTADA
                        </option>
                        <option value="EN_PROCESO" <?php echo ($falta['estado'] == 'EN_PROCESO') ? 'selected' : ''; ?>>
                            🔄 EN PROCESO
                        </option>
                        <option value="SANCIONADA" <?php echo ($falta['estado'] == 'SANCIONADA') ? 'selected' : ''; ?>>
                            ⚠️ SANCIONADA
                        </option>
                        <option value="ARCHIVADA" <?php echo ($falta['estado'] == 'ARCHIVADA') ? 'selected' : ''; ?>>
                            📦 ARCHIVADA
                        </option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </form>
                <div id="mensajeEstado" class="mt-2"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Tipo de Falta</h6>
            </div>
            <div class="card-body">
                <p class="mb-0"><strong><?php echo htmlspecialchars($falta['nombre_tipo']); ?></strong></p>
            </div>
        </div>
    </div>
</div>

<!-- Descripción de la Falta -->
<div class="card mb-3">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="bi bi-file-text"></i> Descripción de la Falta</h6>
    </div>
    <div class="card-body">
        <p class="mb-0"><?php echo nl2br(htmlspecialchars($falta['descripcion_falta'] ?? 'No hay descripción disponible')); ?></p>
    </div>
</div>

<!-- Descargos del Estudiante -->
<div class="card mb-3">
    <div class="card-header" style="background-color: #e3f2fd;">
        <h6 class="mb-0"><i class="bi bi-chat-left-quote"></i> Descargos del Estudiante</h6>
    </div>
    <div class="card-body">
        <?php if (!empty($falta['descargos_estudiante'])): ?>
            <p class="mb-0"><?php echo nl2br(htmlspecialchars($falta['descargos_estudiante'])); ?></p>
        <?php else: ?>
            <p class="text-muted mb-0">No hay descargos registrados</p>
        <?php endif; ?>
    </div>
</div>

<!-- Correctivos Disciplinarios -->
<div class="card mb-3">
    <div class="card-header" style="background-color: #fff3cd;">
        <h6 class="mb-0"><i class="bi bi-clipboard-check"></i> Correctivos Disciplinarios</h6>
    </div>
    <div class="card-body">
        <?php if (!empty($falta['correctivos_disciplinarios'])): ?>
            <p class="mb-0"><?php echo nl2br(htmlspecialchars($falta['correctivos_disciplinarios'])); ?></p>
        <?php else: ?>
            <p class="text-muted mb-0">No hay correctivos registrados</p>
        <?php endif; ?>
    </div>
</div>

<!-- Compromisos -->
<div class="card mb-3">
    <div class="card-header" style="background-color: #d1ecf1;">
        <h6 class="mb-0"><i class="bi bi-hand-thumbs-up"></i> Compromisos</h6>
    </div>
    <div class="card-body">
        <?php if (!empty($falta['compromisos'])): ?>
            <p class="mb-0"><?php echo nl2br(htmlspecialchars($falta['compromisos'])); ?></p>
        <?php else: ?>
            <p class="text-muted mb-0">No hay compromisos registrados</p>
        <?php endif; ?>
    </div>
</div>

<!-- Observaciones -->
<div class="card mb-3">
    <div class="card-header" style="background-color: #f8d7da;">
        <h6 class="mb-0"><i class="bi bi-journal-text"></i> Observaciones</h6>
    </div>
    <div class="card-body">
        <?php if (!empty($falta['observaciones'])): ?>
            <p class="mb-0"><?php echo nl2br(htmlspecialchars($falta['observaciones'])); ?></p>
        <?php else: ?>
            <p class="text-muted mb-0">No hay observaciones registradas</p>
        <?php endif; ?>
    </div>
</div>

<?php
} catch (Exception $e) {
    error_log("Error en obtener_detalle_falta_admin.php: " . $e->getMessage());
    echo '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> Error al cargar el detalle de la falta.</div>';
}
?>
