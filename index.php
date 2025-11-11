<?php
session_start();
if(isset($_SESSION['usuario'])) {
    // Redirigir según el rol del usuario
    $rutas_por_rol = [
        1 => 'views/admin/index.php',
        2 => 'views/docente/index.php',
        3 => 'views/estudiante/index.php'
    ];
    
    $role_id = isset($_SESSION['usuario']['id_rol']) ? intval($_SESSION['usuario']['id_rol']) : 0;
    
    if (isset($rutas_por_rol[$role_id])) {
        header('Location: ' . $rutas_por_rol[$role_id]);
    } else {
        // Si no se puede determinar el rol, cerrar sesión
        session_destroy();
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Faltas - Login</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-icons.min.css">
</head>
<body>
    <!--contenedor general-->
    <div class="container-fluid p-0">
        <!--fila general-->
        <div class="row g-0 min-vh-100">
            <!--columna 1-->
            <div class="col-12 col-md-6 d-flex justify-content-center align-items-center" 
                 style="background-image: url('assets/img/fondo2.jpg'); 
                        background-size: cover; 
                        background-repeat: no-repeat; 
                        background-position: center;"> 
                <img src="assets/img/logo.png" style="width: 50%;" alt="Logo institucional">
            </div>
            <!--columna 2-->
            <div class="col-12 col-md-6 d-flex justify-content-center align-items-center"> 
                <div class="formulario" style="width: 90%;">
                    <div class="row mt-2 d-flex justify-content-center">
                        <div class="col-12 col-md-8 text-center">
                            <form action="views/auth/login.php" method="POST">
                                <h2>Login - Iniciar Sesión</h2>
                                <?php if(isset($_GET['error'])): ?>
                                    <div class="alert alert-danger mt-3">
                                        <?php 
                                        switch($_GET['error']) {
                                            case 'empty':
                                                echo "Por favor complete todos los campos";
                                                break;
                                            case 'invalid':
                                                echo "Usuario o contraseña incorrectos";
                                                break;
                                            case 'inactive':
                                                echo "Su cuenta está inactiva. Contacte al administrador";
                                                break;
                                            case 'role':
                                                echo "Error en la asignación de rol. Contacte al administrador";
                                                break;
                                            case 'db':
                                                echo "Error de conexión con la base de datos";
                                                break;
                                            default:
                                                echo "Error al iniciar sesión. Intente nuevamente";
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
                        </div>
                    </div>

                    <div class="row mt-4 d-flex justify-content-center">
                        <div class="col-12 col-md-2">
                            <label for="usuario" class="form-label">Usuario</label>
                        </div>
                        <div class="col-12 col-md-6">
                            <input type="text" name="usuario" id="usuario" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mt-3 d-flex justify-content-center">
                        <div class="col-12 col-md-2">
                            <label for="clave" class="form-label">Clave</label>
                        </div>
                        <div class="col-12 col-md-6">
                            <input type="password" name="clave" id="clave" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mt-4 d-flex justify-content-center">
                        <div class="col-12 col-md-8 text-center">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/bootstrap.bundle.min.js"></script>    
</body>
</html>