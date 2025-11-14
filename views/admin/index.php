<?php
session_start();

// Verificar si el usuario está logueado y es administrador
if (
    !isset($_SESSION['usuario']) ||
    !isset($_SESSION['usuario']['rol']) ||
    strtolower($_SESSION['usuario']['rol']) !== 'directivo'
) {
    header('Location: ../../index.php');
    exit();
}

// Debug de sesión
error_log("Sesión en admin/index.php: " . print_r($_SESSION, true));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../../login/Bootstrap/bootstrap-icons.min.css">
    <!-- jQuery first, then Bootstrap Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../js/bootstrap.bundle.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: white;
            min-height: 100vh;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 0.8rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }

        .welcome {
            font-size: 1.5rem;
            color: #333;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: 4rem;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 0.6rem 1.5rem;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
            font-weight: 500;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .hamburger {
            position: fixed;
            top: 0.7rem;
            left: 1rem;
            width: 35px;
            height: 30px;
            cursor: pointer;
            z-index: 1001;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .hamburger span {
            width: 100%;
            height: 4px;
            background: #333;
            border-radius: 2px;
            transition: all 0.3s;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(10px, 10px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(8px, -8px);
        }

        .sidebar {
            position: fixed;
            left: -300px;
            top: 0;
            width: 280px;
            height: 100vh;
            background: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: left 0.3s;
            z-index: 999;
            overflow-y: auto;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            background: white;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
        }

        .sidebar-header h2 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }

        .menu-list {
            list-style: none;
            padding: 1rem 0;
        }

        .menu-item {
            padding: 1rem 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            color: #333;
            font-weight: 500;
        }

        .menu-item:hover {
            background: #f8f9fa;
            border-left-color: #667eea;
            color: #667eea;
        }

        .menu-item.active {
            background: #f8f9fa;
            border-left-color: #667eea;
            color: #667eea;
        }

        .content {
            margin-left: 0;
            margin-top: 70px;
            padding: 2rem;
            transition: margin-left 0.3s;
            display: flex;
            justify-content: center;
            opacity: 1;
        }

        .table-section {
            display: none;
            width: 100%;
            max-width: 1200px;
            animation: fadeIn 0.2s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .table-section.active {
            display: block;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 998;
        }

        .overlay.active {
            display: block;
        }

        .welcome-message {
            text-align: center;
            padding: 4rem 2rem;
        }

        .welcome-message h2 {
            color: #667eea;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .welcome-message p {
            color: #7f8c8d;
            font-size: 1.2rem;
        }

        .welcome-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .header {
                padding: 1rem;
            }

            .welcome {
                font-size: 1.2rem;
            }

            .hamburger {
                left: 1rem;
            }

            .content {
                padding: 1rem;
            }
        }
    </style>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/admin.css">
    <!-- jQuery first, then Bootstrap Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../js/admin.js" defer></script>
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
            <h2>📊 Menú Administrativo</h2>
            <p>Sistema Disciplinario</p>
        </div>
        <ul class="menu-list">
            <li class="menu-item" onclick="showTable(1)">👨‍🏫 Docentes</li>
            <li class="menu-item" onclick="showTable(2)">👥 Estudiantes</li>
            <li class="menu-item" onclick="showTable(3)">📋 Asignaturas</li>
            <li class="menu-item" onclick="showTable(4)">📚 Cursos</li>
            <li class="menu-item" onclick="showTable(5)">👨‍🏫 Asignar Docente</li>
            <li class="menu-item" onclick="showTable(6)">📅 Faltas</li>

        </ul>
    </div>

    <div class="header">
        <div class="welcome" onclick="showTable(0)" style="cursor: pointer;">
            <span>Bienvenido Administrador</span>
            <strong><?php echo htmlspecialchars($_SESSION['usuario']['nombres'] . ' ' . $_SESSION['usuario']['apellidos']); ?></strong>
        </div>
        <a href="../auth/logout.php" class="logout-btn">🚪 Cerrar Sesión</a>
    </div>

    <div class="content">
        <div class="table-section active" id="table0">
            <div class="welcome-message">
                <div class="welcome-icon">🖥</div>
                <h2>Panel Administrativo</h2>
                <p>Seleccione una opción del menú para comenzar</p>
            </div>
            <div class="logo-container" style="text-align: center; margin-top: 2rem;">
                <img src="../../assets/img/logo.png" alt="Logo institucional" style="width: 400px;">

            </div>
        </div>

        <div class="table-section" id="table1">
            <?php include 'docentes.php'; ?>
        </div>
        <div class="table-section" id="table2">
            <?php include 'estudiantes.php'; ?>
        </div>

        <div class="table-section" id="table3">
            <?php include 'asignaturas.php'; ?>

        </div>
        <div class="table-section" id="table4">
            <?php include 'curso.php'; ?>
        </div>
        <div class="table-section" id="table5">
            <?php include 'asignar_curso_docente.php'; ?>
        </div>
        <div class="table-section" id="table6">
            <?php include 'faltas.php'; ?>
        </div>
    </div>

    <!-- Variables PHP para JavaScript -->
    <script>
        // Variable para detectar si es una nueva sesión
        var isNewSession = <?php echo !isset($_SESSION['session_started']) ? 'true' : 'false'; ?>;

        // Detectar si se hizo un POST (submit de formulario)
        var isFormSubmit = <?php echo ($_SERVER['REQUEST_METHOD'] === 'POST') ? 'true' : 'false'; ?>;

        <?php
        // Marcar que la sesión ya está iniciada
        if (!isset($_SESSION['session_started'])) {
            $_SESSION['session_started'] = true;
        }
        ?>
    </script>

</body>

</html>