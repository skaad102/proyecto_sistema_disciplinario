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
            // Limpiar sesión anterior completamente
            session_start();
            session_unset();
            session_destroy();
            session_start();
            session_regenerate_id(true);
            
            // Debug - Imprimir todos los datos del usuario
            error_log("Datos del usuario: " . print_r($user, true));
            
            $_SESSION['usuario'] = [
                'id' => $user['cod_usuario'],
                'usuario' => $user['usuario'],
                'nombres' => $user['nombres'],
                'apellidos' => $user['apellidos'], 
                'rol' => $user['rol'],
                'id_rol' => $user['id_rol']
            ];

            // Determinar la redirección basada en id_rol
            $redirect_url = '';
            
            // Debug - Verificar el tipo de id_rol
            error_log("Tipo de id_rol: " . gettype($user['id_rol']));
            error_log("Valor de id_rol: " . $user['id_rol']);
            
            // Forzar la conversión a entero
            $role_id = intval($user['id_rol']);
            
            error_log("Role ID después de conversión: " . $role_id);
            
            if ($role_id === 2) {
                $redirect_url = '../../views/docente/index.php';
                error_log("Redirigiendo a: " . $redirect_url);
            } 
            elseif ($role_id === 1) {
                $redirect_url = '../../views/dashboard.php';
            }
            elseif ($role_id === 3) {
                $redirect_url = '../../views/estudiante/index.php';
            }
            else {
                error_log("Role ID no reconocido: " . $role_id);
                session_destroy();
                header('Location: ../../index.php?error=role');
                exit();
            }
            
            if (!empty($redirect_url)) {
                error_log("Ejecutando redirección a: " . $redirect_url);
                // Forzar que no se use caché
                header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
                header("Location: " . $redirect_url);
                exit();
            }            // Redirigir según el id_rol
            if ($user['id_rol'] == 2) {
                header('Location: ../../views/docente/index.php');
                exit();
            } elseif ($user['id_rol'] == 1) {
                header('Location: ../../views/dashboard.php');
                exit();
            } elseif ($user['id_rol'] == 3) {
                header('Location: ../../views/estudiante/index.php');
                exit();
            } else {
                session_destroy();
                header('Location: ../../index.php?error=role');
                exit();
            }

            // Debug - Mostrar el rol y el id_rol
            error_log("ID del rol: " . $user['id_rol']);

            // Registrar en el log los datos del usuario
            $log = "=== Datos del usuario ===\n";
            $log .= "Usuario: " . $user['usuario'] . "\n";
            $log .= "ID del rol: " . $user['id_rol'] . "\n";
            $log .= "Rol: " . $user['rol'] . "\n";
            file_put_contents($logFile, $log, FILE_APPEND);

            // Redirigir según el rol usando id_rol
            if ($user['id_rol'] == 1) {
                file_put_contents($logFile, "Redirigiendo a dashboard\n", FILE_APPEND);
                header('Location: ../../views/dashboard.php');
            } elseif ($user['id_rol'] == 2) {
                file_put_contents($logFile, "Redirigiendo a panel de docente\n", FILE_APPEND);
                header('Location: ../../views/docente/index.php');
            } elseif ($user['id_rol'] == 3) {
                file_put_contents($logFile, "Redirigiendo a panel de estudiante\n", FILE_APPEND);
                header('Location: ../../views/estudiante/index.php');
            } else {
                // Log del rol no reconocido
                error_log("Rol no reconocido: " . $user['rol'] . " para usuario: " . $user['usuario']);
                session_destroy();
                header('Location: ../../index.php?error=role');
            }
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
