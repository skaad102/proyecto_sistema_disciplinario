<?php
// Verificar si hay una sesión activa y si el usuario es un estudiante
if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol']) !== 'estudiante') {
    header('Location: ../../index.php');
    exit();
}

// Debug de sesión
error_log("Datos de sesión en mis_asignaturas.php: " . print_r($_SESSION, true));

// Incluir archivos necesarios
require_once(__DIR__ . '/../../config/database.php');
require_once(__DIR__ . '/../../includes/estudiante_functions.php');

// Crear conexión a la base de datos
$database = new Database();
$conexion = $database->connect();

try {
    // Debug de la información del usuario
    error_log("Verificando datos de sesión en mis_asignaturas.php");
    error_log("SESSION completa: " . print_r($_SESSION, true));
    
    // Verificar y obtener el ID del usuario
    if (isset($_SESSION['usuario']['id'])) {
        $id_usuario = $_SESSION['usuario']['id'];
    } else if (isset($_SESSION['usuario']['cod_usuario'])) {
        $id_usuario = $_SESSION['usuario']['cod_usuario'];
    } else {
        throw new Exception("No se pudo encontrar el ID del usuario en la sesión");
    }
    
    error_log("ID de usuario encontrado: " . $id_usuario);
    
    // Obtener el ID del estudiante usando la función
    $id_estudiante = obtenerIdEstudiante($conexion, $id_usuario);
    error_log("ID de estudiante obtenido: " . ($id_estudiante ?? 'null'));
    
    if ($id_estudiante === null) {
        throw new Exception("No se encontró el registro de estudiante para este usuario.");
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    die("Error: No se pudo obtener la información del estudiante");
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">📚 Mis Asignaturas</h2>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Curso</th>
                            <th>Director de Grupo</th>
                            <th>Año Lectivo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            // Obtener los cursos del estudiante
                            $cursos = obtenerCursosEstudiante($conexion, $id_estudiante);

                            if (empty($cursos)) {
                                echo "<tr><td colspan='4' class='text-center'>No se encontraron cursos matriculados</td></tr>";
                            }

                            foreach ($cursos as $curso) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($curso['nombre_curso']) . "</td>";
                                echo "<td>" . htmlspecialchars($curso['director_grupo']) . "</td>";
                                echo "<td>" . htmlspecialchars($curso['ano_lectivo']) . "</td>";
                                echo "<td>" . htmlspecialchars($curso['estado']) . "</td>";
                                echo "</tr>";
                            }
                        } catch (PDOException $e) {
                            error_log("Error en mis_asignaturas.php: " . $e->getMessage());
                            echo "<tr><td colspan='4' class='text-center'>Error al cargar las asignaturas</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>