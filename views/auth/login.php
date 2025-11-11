<?php
// Mostrar todos los errores en el navegador
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurar zona horaria para los logs
date_default_timezone_set('America/Bogota');
ini_set('error_log', 'C:/xampp/htdocs/app/logs/debug.log');

// Crear directorio de logs si no existe
$logDir = 'C:/xampp/htdocs/app/logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

// Verificar que podemos escribir en el archivo
$logFile = $logDir . '/debug.log';
file_put_contents($logFile, "=== Inicio de sesión: " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
error_log("=== Nueva solicitud de inicio de sesión ===");

require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $clave = $_POST['clave'] ?? '';

    if (empty($usuario) || empty($clave)) {
        header('Location: ../../index.php?error=empty');
        exit;
    }

    try {
        $db = new Database();
        $conn = $db->connect();

        // Log del intento de inicio de sesión
        error_log("Intento de inicio de sesión - Usuario: " . $usuario);

        $log = "Intentando autenticar usuario: " . $usuario . "\n";
        file_put_contents($logFile, $log, FILE_APPEND);

        $stmt = $conn->prepare("SELECT u.*, r.rol, r.cod_rol as id_rol 
                              FROM usuario u 
                              JOIN rol r ON u.id_rol = r.cod_rol 
                              WHERE u.usuario = ? AND u.clave = ?");

        $stmt->execute([$usuario, $clave]);
        $log = "Consulta ejecutada. Número de filas: " . $stmt->rowCount() . "\n";
        file_put_contents($logFile, $log, FILE_APPEND);

        // Log del resultado de la consulta
        if ($stmt->rowCount() === 0) {
            error_log("Inicio de sesión fallido - Usuario: " . $usuario);
        }
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Iniciar sesión limpia
            session_start();
            session_unset();
            session_destroy();
            session_start();
            session_regenerate_id(true);

            // Registrar datos del usuario en el log
            error_log("Datos del usuario: " . print_r($user, true));

            // Guardar datos en la sesión
            $_SESSION['usuario'] = [
                'id' => $user['cod_usuario'],
                'usuario' => $user['usuario'],
                'nombres' => $user['nombres'],
                'apellidos' => $user['apellidos'],
                'rol' => $user['rol'],
                'id_rol' => intval($user['id_rol'])
            ];

            // Definir rutas de redirección según el rol
            $rutas_por_rol = [
                1 => '../../views/admin/index.php',
                2 => '../../views/docente/index.php',
                3 => '../../views/estudiante/index.php'
            ];

            $role_id = intval($user['id_rol']);

            // Verificar si el rol existe en las rutas definidas
            if (!isset($rutas_por_rol[$role_id])) {
                error_log("Rol no reconocido: ID={$role_id}, Nombre={$user['rol']} para usuario: {$user['usuario']}");
                session_destroy();
                header('Location: ../../index.php?error=role');
                exit();
            }

            // Obtener la ruta de redirección
            $redirect_url = $rutas_por_rol[$role_id];

            // Log de redirección
            error_log("Usuario: {$user['usuario']}, Rol: {$user['rol']} (ID: {$role_id}), Redirigiendo a: {$redirect_url}");

            // Prevenir caché y redirigir
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header("Location: " . $redirect_url);
            exit;
        } else {
            // Verificar si el usuario existe pero está inactivo
            $stmt = $conn->prepare("SELECT estado FROM usuario WHERE usuario = ?");
            $stmt->execute([$usuario]);
            $userStatus = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userStatus && $userStatus['estado'] !== 'ACTIVO') {
                header('Location: ../../index.php?error=inactive');
            } else {
                header('Location: ../../index.php?error=invalid');
            }
            exit;
        }
    } catch (PDOException $e) {
        // Log del error para el administrador
        error_log("Error de base de datos: " . $e->getMessage());
        header('Location: ../../index.php?error=db');
        exit;
    }
}
