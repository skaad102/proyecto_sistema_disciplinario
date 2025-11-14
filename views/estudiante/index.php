<?php
session_start();

// Verificar si el usuario está logueado y es estudiante
if (
    !isset($_SESSION['usuario']) ||
    !isset($_SESSION['usuario']['rol']) ||
    strtolower($_SESSION['usuario']['rol']) !== 'estudiante'
) {
    header('Location: ../../index.php');
    exit();
}

// Debug de sesión
error_log("Sesión en estudiante/index.php: " . print_r($_SESSION, true));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Estudiante</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../../Bootstrap/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/estudiante.css">
    <!-- jQuery first, then Bootstrap Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../js/estudiante.js" defer></script>
    <link rel="icon" href="../../assets/img/logo.png" type="image/png">

</head>

<body>
    <div class="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div class="overlay"></div>

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>📚 Menú Estudiante</h2>
            <p>Sistema Disciplinario</p>
        </div>
        <ul class="menu-list">
            <li class="menu-item" onclick="showTable(1)">📚 Mis Asignaturas</li>
            <li class="menu-item" onclick="showTable(2)">📚 Mis Reportes</li>
        </ul>
    </div>

    <div class="header">
        <div class="welcome" onclick="showTable(0)" style="cursor: pointer;">
            <span>Bienvenido Estudiante</span>
            <strong><?php echo htmlspecialchars($_SESSION['usuario']['nombres'] . ' ' . $_SESSION['usuario']['apellidos']); ?></strong>
        </div>
        <a href="../auth/logout.php" class="logout-btn">🚪 Cerrar Sesión</a>
    </div>

    <div class="content">
        <div class="table-section active" id="table0">
            <div class="welcome-message">
                <div class="welcome-icon">👨‍🎓</div>
                <h2>Panel de Estudiante</h2>
                <p>Seleccione una opción del menú para comenzar</p>
            </div>
            <div class="logo-container" style="text-align: center; margin-top: 2rem;">
                <img src="../../assets/img/logo.png" alt="Logo institucional" style="width: 400px;">

            </div>
        </div>

        <div class="table-section" id="table1">
            <?php include 'mis_asignaturas.php'; ?>
        </div>
        <div class="table-section" id="table2">
            <?php include 'mis_reportes.php'; ?>
        </div>
    </div>

    <script>
        localStorage.setItem('isNewSession', <?php echo !isset($_SESSION['session_started']) ? 'true' : 'false'; ?>);
        <?php
        if (!isset($_SESSION['session_started'])) {
            $_SESSION['session_started'] = true;
        }
        ?>
    </script>
</body>

</html>